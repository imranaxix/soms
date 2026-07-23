<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\JazzCashService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $manufacturer = $order->manufacturer;

        if (!$manufacturer || (!$manufacturer->hasJazzCash() && !$manufacturer->hasStripe())) {
            return redirect()->route('shop.orders.show', $order->id)
                ->with('error', 'This manufacturer has not configured any payment methods yet. Please contact them to complete setup before making a payment.');
        }

        $balanceDue = $order->total_amount - $order->paid_amount;
        return view('shop-owner.orders.pay', compact('order', 'balanceDue'));
    }

    /**
     * Initiate Stripe Payment - creates a PaymentIntent
     */
    public function initiateStripePayment(Request $request, Order $order)
    {
        if ($order->shop_owner_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $manufacturer = $order->manufacturer;
        if (!$manufacturer || !$manufacturer->hasStripe()) {
            return response()->json(['error' => 'Stripe is not configured for this manufacturer.'], 422);
        }

        $balanceDue = $order->total_amount - $order->paid_amount;

        $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:' . $balanceDue],
        ]);

        $amountInCents = intval(round($request->amount * 100));

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Create a PaymentIntent direct to Connected Account
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'pkr',
                'description' => "Order #{$order->id} payment via SOMS Platform",
            ], [
                'stripe_account' => $manufacturer->stripe_connect_id,
            ]);

            // Save pending payment record locally
            $txnRefNo = 'S' . date('ymdHis') . $order->id . strtoupper(Str::random(3));
            Payment::create([
                'order_id'   => $order->id,
                'payer_id'   => Auth::id(),
                'payee_id'   => $order->manufacturer_id,
                'amount'     => $request->amount,
                'txn_ref_no' => $txnRefNo,
                'stripe_payment_intent_id' => $paymentIntent->id,
                'status'     => 'pending',
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'publishableKey' => config('services.stripe.key'),
                'connectedAccountId' => $manufacturer->stripe_connect_id
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Confirm Stripe Payment - call after frontend confirmation succeeds
     */
    public function confirmStripePayment(Request $request, Order $order)
    {
        if ($order->shop_owner_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'payment_intent_id' => 'required|string',
        ]);

        $payment = Payment::where('order_id', $order->id)
            ->where('stripe_payment_intent_id', $request->payment_intent_id)
            ->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment record not found.'], 404);
        }

        if ($payment->status === 'completed') {
            return response()->json(['success' => true]);
        }

        // Validate the payment with Stripe API
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        try {
            $paymentIntent = \Stripe\PaymentIntent::retrieve(
                $request->payment_intent_id,
                [],
                ['stripe_account' => $order->manufacturer->stripe_connect_id]
            );

            if ($paymentIntent->status === 'succeeded') {
                DB::transaction(function () use ($payment, $order) {
                    $payment->update([
                        'status' => 'completed',
                        'paid_at' => now(),
                    ]);

                    $order->increment('paid_amount', $payment->amount);
                });

                return response()->json(['success' => true]);
            }

            return response()->json(['error' => 'Payment status is: ' . $paymentIntent->status], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
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

        $request->validate([
            'amount'            => ['required', 'numeric', 'min:1', 'max:' . $balanceDue],
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

        $salt = '951zt9xe85';

        $regeneratedHash = $this->jazzCashService->generateSecureHash($incomingData, $salt);
        $receivedHash = $request->input('pp_SecureHash');

        if (strtoupper($regeneratedHash) !== strtoupper($receivedHash)) {
            Log::error("JazzCash Return: Secure signature validation mismatch.");
            return redirect()->route('shop.orders.show', $order->id)->with('error', 'Payment signature verification failed.');
        }

        $responseCode = $request->input('pp_ResponseCode');
        $responseMessage = $request->input('pp_ResponseMessage');
        
        if (!in_array($payment->status, ['failed', 'completed']) || $responseCode === '000') {
            if ($responseCode === '000') {
                DB::transaction(function () use ($payment, $order, $request, $responseCode, $responseMessage) {
                    $payment->update([
                        'status'  => 'completed',
                        'paid_at' => now(),
                        'pp_txn_id'           => $request->input('pp_TxnRefNo'),
                        'pp_response_code'    => $responseCode,
                        'pp_response_message' => $responseMessage,
                        'pp_retrieval_ref_no' => $request->input('pp_RetreivalReferenceNo') ?? $request->input('pp_RetrievalReferenceNo'),
                    ]);

                    $order->increment('paid_amount', $payment->amount);
                });

                Log::info("JazzCash Return: Payment successful for order ID: {$order->id}");
                
                return redirect()->route('shop.orders.show', $order->id)->with('success', 'Payment was successful!');
            } else {
                $payment->update([
                    'status' => 'failed',
                    'pp_response_code'    => $responseCode,
                    'pp_response_message' => $responseMessage,
                ]);
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