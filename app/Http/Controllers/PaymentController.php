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
        if ($order->shop_owner_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$order->manufacturer || !$order->manufacturer->hasJazzCash()) {
            return redirect()->route('shop.orders.show', $order->id)
                ->with('error', 'This manufacturer has not yet configured their JazzCash merchant credentials. Please contact them to complete setup before making a payment.');
        }

        $balanceDue = $order->total_amount - $order->paid_amount;
        return view('shop-owner.orders.pay', compact('order', 'balanceDue'));
    }

    /**
     * Initiate payment - HTTP POST Page Redirect Method
     */
    public function initiatePayment(Request $request, Order $order)
    {
        if ($order->shop_owner_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$order->manufacturer || !$order->manufacturer->hasJazzCash()) {
            return redirect()->route('shop.orders.show', $order->id)
                ->with('error', 'Payment cannot be initiated: the manufacturer has not configured their JazzCash merchant credentials.');
        }

        $balanceDue = $order->total_amount - $order->paid_amount;

        // Temporarily removed max validation so you can test larger amounts (e.g. 100 PKR)
        $request->validate([
            'amount'            => ['required', 'numeric', 'min:1'],
            'shop_owner_mobile' => ['required', 'regex:/^03[0-9]{9}$/'],
            'cnic'              => ['required', 'regex:/^[0-9]{6}$/'],
        ]);

        $txnRefNo = 'T' . date('ymdHis') . $order->id . strtoupper(Str::random(3));

        $payment = Payment::create([
            'order_id'   => $order->id,
            'payer_id'   => Auth::id(),
            'payee_id'   => $order->manufacturer_id,
            'amount'     => $request->amount,
            'txn_ref_no' => $txnRefNo,
            'status'     => 'pending',
        ]);

        $payload = $this->jazzCashService->buildRedirectPayload(
            $request->amount,
            $txnRefNo,
            $request->shop_owner_mobile,
            $order->manufacturer,
            $request->cnic
        );

        Log::info('JazzCash redirect payload built for txn: ' . $txnRefNo);

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
     * Callback handler (Browser redirect from JazzCash).
     */
    public function callback(Request $request)
    {
        Log::info('JazzCash Browser Return payload received: ', $request->all());

        $incomingData = $request->except(['/']);
        $txnRefNo = $request->input('pp_TxnRefNo');

        $payment = Payment::where('txn_ref_no', $txnRefNo)->first();

        if (!$payment) {
            Log::error('JazzCash Return: Payment record not found for reference: ' . $txnRefNo);
            return redirect()->route('shop.orders.index')->with('error', 'Payment record not found.');
        }

        $order = $payment->order;
        $manufacturer = $payment->payee;
        
        if (!$manufacturer) {
            return redirect()->route('shop.orders.show', $order->id)->with('error', 'Manufacturer profile missing.');
        }

        $salt = '2ss4g2u62u';

        $regeneratedHash = $this->jazzCashService->generateSecureHash($incomingData, $salt);
        $receivedHash = $request->input('pp_SecureHash');

        if (strtoupper($regeneratedHash) !== strtoupper($receivedHash)) {
            Log::error("JazzCash Return: Secure signature validation mismatch.");
            return redirect()->route('shop.orders.show', $order->id)->with('error', 'Payment signature verification failed.');
        }

        $responseCode = $request->input('pp_ResponseCode');
        $responseMessage = $request->input('pp_ResponseMessage');
        
        if (!in_array($payment->status, ['failed', 'completed']) || $responseCode === '000') {
            $payment->update([
                'pp_txn_id'           => $request->input('pp_TxnRefNo'),
                'pp_response_code'    => $responseCode,
                'pp_response_message' => $responseMessage,
                'pp_retrieval_ref_no' => $request->input('pp_RetreivalReferenceNo') ?? $request->input('pp_RetrievalReferenceNo'),
            ]);

            if ($responseCode === '000') {
                $payment->update([
                    'status'  => 'completed',
                    'paid_at' => now(),
                ]);

                $order->increment('paid_amount', $payment->amount);
                Log::info("JazzCash Return: Payment successful for order ID: {$order->id}");
                
                return redirect()->route('shop.orders.show', $order->id)->with('success', 'Payment was successful!');
            } else {
                $payment->update(['status' => 'failed']);
                Log::warning("JazzCash Return: Payment failed with code {$responseCode}");
                
                return redirect()->route('shop.orders.show', $order->id)->with('error', "Payment Failed: " . $responseMessage);
            }
        }
        
        // If already processed
        if ($payment->status === 'completed') {
             return redirect()->route('shop.orders.show', $order->id)->with('success', 'Payment was already successful!');
        }

        return redirect()->route('shop.orders.show', $order->id)->with('error', "Payment Failed: " . $responseMessage);
    }
}