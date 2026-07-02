<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\JazzCashService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class PaymentController extends Controller
{
    protected $jazzCashService;

    public function __construct(JazzCashService $jazzCashService)
    {
        $this->jazzCashService = $jazzCashService;
    }

    /**
     * Show the payment form.
     */
    public function showPaymentForm(Order $order)
    {
        // Authorize the logged-in user is the order's shop owner
        if ($order->shop_owner_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Block access if the manufacturer hasn't configured their full JazzCash merchant credentials
        if (!$order->manufacturer || !$order->manufacturer->hasJazzCash()) {
            return redirect()->route('shop.orders.show', $order->id)
                ->with('error', 'This manufacturer has not yet configured their JazzCash merchant credentials. Please contact them to complete setup before making a payment.');
        }

        $balanceDue = $order->total_amount - $order->paid_amount;

        return view('shop-owner.orders.pay', compact('order', 'balanceDue'));
    }

    /**
     * Initiate payment.
     */
    public function initiatePayment(Request $request, Order $order)
    {
        // Authorize the logged-in user is the order's shop owner
        if ($order->shop_owner_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Guard: manufacturer must have all three merchant credentials configured
        if (!$order->manufacturer || !$order->manufacturer->hasJazzCash()) {
            return redirect()->route('shop.orders.show', $order->id)
                ->with('error', 'Payment cannot be initiated: the manufacturer has not configured their JazzCash merchant credentials (Merchant ID, Password, and Integrity Salt).');
        }

        $balanceDue = $order->total_amount - $order->paid_amount;

        // Validate amount, shop owner mobile number, and last 6 digits of CNIC
        $request->validate([
            'amount'       => ['required', 'numeric', 'min:1', 'max:' . $balanceDue],
            'shop_owner_mobile' => ['required', 'regex:/^03[0-9]{9}$/'],
            'cnic'         => ['required', 'regex:/^[0-9]{6}$/'],
        ], [
            'shop_owner_mobile.regex' => 'Mobile number must be a valid Pakistani number starting with 03 (e.g. 03001234567).',
            'cnic.regex'   => 'Please provide the last 6 digits of your CNIC.',
        ]);

        // Generate a unique txn_ref_no (format T{Ymd}ORD{order_id})
        // Appending a microtime/timestamp ensures it is globally unique and doesn't collide on retries
        $txnRefNo = 'T' . date('ymdHis') . $order->id . strtoupper(Str::random(3));

        // Create a Payment record with status=pending
        $payment = Payment::create([
            'order_id' => $order->id,
            'payer_id' => Auth::id(),
            'payee_id' => $order->manufacturer_id,
            'amount' => $request->amount,
            'txn_ref_no' => $txnRefNo,
            'status' => 'pending',
        ]);

        // Call JazzCashService::processWalletPayment passing the order's manufacturer and CNIC
        $response = $this->jazzCashService->processWalletPayment(
            $request->amount,
            $txnRefNo,
            $request->shop_owner_mobile,
            $order->manufacturer,
            $request->cnic
        );

        Log::info('JazzCash initiation response: ', $response);

        if (isset($response['pp_ResponseCode'])) {
            $payment->update([
                'pp_txn_id' => $response['pp_TxnRefNo'] ?? null,
                'pp_response_code' => $response['pp_ResponseCode'],
                'pp_response_message' => $response['pp_ResponseMessage'] ?? null,
                'pp_retrieval_ref_no' => $response['pp_RetrievalReferenceNo'] ?? null,
            ]);

            if (in_array($response['pp_ResponseCode'], ['000', '124', '200'])) {
                return redirect()->route('shop.orders.show', $order->id)
                    ->with('success', 'Payment initiated successfully. ' . ($response['pp_ResponseMessage'] ?? ''));
            } else {
                $payment->update(['status' => 'failed']);
                return back()->withErrors(['error' => 'Payment Failed: ' . ($response['pp_ResponseMessage'] ?? 'Unknown Error')]);
            }
        }

        $payment->update(['status' => 'failed']);
        return back()->withErrors(['error' => 'Failed to connect to payment gateway.']);
    }

    /**
     * Get the latest payment status for polling.
     */
    public function getPaymentStatus(Order $order)
    {
        if ($order->shop_owner_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $latestPayment = $order->payments()->latest()->first();

        return response()->json([
            'status' => $latestPayment ? $latestPayment->status : null,
            'paid_amount' => $order->paid_amount,
            'balance' => $order->total_amount - $order->paid_amount,
        ]);
    }

    /**
     * Callback handler (public webhook, no auth).
     */
    public function callback(Request $request)
    {
        Log::info('JazzCash Callback payload: ', $request->all());

        $txnRefNo = $request->input('pp_TxnRefNo');

        // Look up the Payment by pp_TxnRefNo (or pp_TxnRefNo column/variable)
        $payment = Payment::where('txn_ref_no', $txnRefNo)->first();

        if (!$payment) {
            Log::error('JazzCash Callback: Payment record not found for reference: ' . $txnRefNo);
            return response()->json(['error' => 'Payment record not found'], 404);
        }

        // Load the payee (manufacturer)
        $manufacturer = $payment->payee;
        if (!$manufacturer) {
            Log::error('JazzCash Callback: Manufacturer not found for payment ID: ' . $payment->id);
            return response()->json(['error' => 'Manufacturer not found'], 404);
        }

        // Decrypt their integrity salt (handled automatically via cast)
        $salt = $manufacturer->jazzcash_integrity_salt;

        if (empty($salt)) {
            Log::error('JazzCash Callback: Integrity salt not configured for manufacturer: ' . $manufacturer->id);
            return response()->json(['error' => 'Integrity salt not configured'], 400);
        }

        // Regenerate secure hash from callback payload and compare
        $regeneratedHash = $this->jazzCashService->generateSecureHash($request->all(), $salt);
        $receivedHash = $request->input('pp_SecureHash');

        if (strtoupper($regeneratedHash) !== strtoupper($receivedHash)) {
            Log::error("JazzCash Callback: Secure hash mismatch. Generated: {$regeneratedHash}, Received: {$receivedHash}");
            return response()->json(['error' => 'Invalid secure hash'], 400);
        }

        // If hash matches and pp_ResponseCode is 000, mark Payment completed
        $responseCode = $request->input('pp_ResponseCode');
        $payment->update([
            'pp_txn_id' => $request->input('pp_TxnRefNo'),
            'pp_response_code' => $responseCode,
            'pp_response_message' => $request->input('pp_ResponseMessage'),
            'pp_retrieval_ref_no' => $request->input('pp_RetrievalReferenceNo'),
        ]);

        if ($responseCode === '000') {
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            // Update the Order's balance
            $order = $payment->order;
            $order->increment('paid_amount', $payment->amount);

            Log::info("JazzCash Callback: Payment completed for order {$order->id}. Amount: {$payment->amount}");
            return response()->json(['status' => 'success']);
        } else {
            $payment->update(['status' => 'failed']);
            Log::warning("JazzCash Callback: Payment failed for order {$payment->order_id} with response code: {$responseCode}");
            return response()->json(['status' => 'failed']);
        }
    }
}