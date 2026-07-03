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
     * Initiate payment - HTTP POST Page Redirect Method
     * Redirects user to JazzCash gateway instead of calling API
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
                ->with('error', 'Payment cannot be initiated: the manufacturer has not configured their JazzCash merchant credentials.');
        }

        $balanceDue = $order->total_amount - $order->paid_amount;

        // Validate amount, shop owner mobile number, and last 6 digits of CNIC
        $request->validate([
            'amount'            => ['required', 'numeric', 'min:1', 'max:' . $balanceDue],
            'shop_owner_mobile' => ['required', 'regex:/^03[0-9]{9}$/'],
            'cnic'              => ['required', 'regex:/^[0-9]{6}$/'],
        ], [
            'shop_owner_mobile.regex' => 'Mobile number must be a valid Pakistani number starting with 03 (e.g. 03001234567).',
            'cnic.regex'               => 'Please provide the last 6 digits of your CNIC.',
        ]);

        // Generate a unique txn_ref_no
        $txnRefNo = 'T' . date('ymdHis') . $order->id . strtoupper(Str::random(3));

        // Create a Payment record with status=pending
        $payment = Payment::create([
            'order_id'   => $order->id,
            'payer_id'   => Auth::id(),
            'payee_id'   => $order->manufacturer_id,
            'amount'     => $request->amount,
            'txn_ref_no' => $txnRefNo,
            'status'     => 'pending',
        ]);

        // Build JazzCash redirect form payload
        $payload = $this->jazzCashService->buildRedirectPayload(
            $request->amount,
            $txnRefNo,
            $request->shop_owner_mobile,
            $order->manufacturer,
            $request->cnic
        );

        Log::info('JazzCash redirect payload built for txn: ' . $txnRefNo);

        // Return HTML form that auto-submits to JazzCash
        return view('shop-owner.orders.jazzcash-redirect', compact('payload'));
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
            'status'      => $latestPayment ? $latestPayment->status : null,
            'paid_amount' => $order->paid_amount,
            'balance'     => $order->total_amount - $order->paid_amount,
        ]);
    }

    /**
     * Callback handler (public webhook, no auth).
     */
    public function callback(Request $request)
    {
        Log::info('JazzCash Callback payload received: ', $request->all());

        // Extract clean incoming fields excluding framework parameters
        $incomingData = $request->except(['/']);
        $txnRefNo = $request->input('pp_TxnRefNo');

        // Look up the Payment by txn_ref_no
        $payment = Payment::where('txn_ref_no', $txnRefNo)->first();

        if (!$payment) {
            Log::error('JazzCash Callback: Payment record not found for reference: ' . $txnRefNo);
            return response()->json(['error' => 'Payment record not found'], 404);
        }

        // Load payee manufacturer profiles
        $manufacturer = $payment->payee;
        if (!$manufacturer) {
            Log::error('JazzCash Callback: Manufacturer profile missing for payment ID: ' . $payment->id);
            return response()->json(['error' => 'Manufacturer profile missing'], 404);
        }

        $salt = $manufacturer->jazzcash_integrity_salt;
        if (empty($salt)) {
            Log::error('JazzCash Callback: Salt string missing for manufacturer target reference ID: ' . $manufacturer->id);
            return response()->json(['error' => 'Integrity salt missing'], 400);
        }

        // Regenerate and verify inbound secure hash matching
        $regeneratedHash = $this->jazzCashService->generateSecureHash($incomingData, $salt);
        $receivedHash = $request->input('pp_SecureHash');

        if (strtoupper($regeneratedHash) !== strtoupper($receivedHash)) {
            Log::error("JazzCash Callback: Secure signature validation mismatch. Expected: {$regeneratedHash}, Received: {$receivedHash}");
            return response()->json(['error' => 'Inbound secure hash signature validation mismatch'], 400);
        }

        // Identify response status elements (Handling intentional gateway typo field 'pp_RetreivalReferenceNo')
        $responseCode = $request->input('pp_ResponseCode');
        $payment->update([
            'pp_txn_id'           => $request->input('pp_TxnRefNo'),
            'pp_response_code'    => $responseCode,
            'pp_response_message' => $request->input('pp_ResponseMessage'),
            'pp_retrieval_ref_no' => $request->input('pp_RetreivalReferenceNo') ?? $request->input('pp_RetrievalReferenceNo'),
        ]);

        if ($responseCode === '000') {
            $payment->update([
                'status'  => 'completed',
                'paid_at' => now(),
            ]);

            // Safely increment order tracking levels
            $order = $payment->order;
            $order->increment('paid_amount', $payment->amount);

            Log::info("JazzCash Callback: Payment updated successfully for order ID: {$order->id}");
            return response()->json(['status' => 'success']);
        } else {
            $payment->update(['status' => 'failed']);
            Log::warning("JazzCash Callback: Flagged failing transactional processing status sequence value: {$responseCode}");
            
            // Standard JSON response to cleanly terminate loop cycles
            return response()->json(['status' => 'failed', 'code' => $responseCode]);
        }
    }
}