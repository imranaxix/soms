<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\SafepayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $safepayService;

    public function __construct(SafepayService $safepayService)
    {
        $this->safepayService = $safepayService;
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

        if (!$manufacturer || (!$manufacturer->hasStripe() && !$this->safepayService->isConfigured($manufacturer))) {
            return redirect()->route('shop.orders.show', $order->id)
                ->with('error', 'This manufacturer has not configured any payment methods yet. Please contact them to complete setup before making a payment.');
        }

        $balanceDue = $order->total_amount - $order->paid_amount;
        $safepayEnabled = $this->safepayService->isConfigured($manufacturer);
        return view('shop-owner.orders.pay', compact('order', 'balanceDue', 'safepayEnabled'));
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

        \Stripe\Stripe::setApiKey($manufacturer->stripe_secret_key);

        try {
            // Create a PaymentIntent directly to manufacturer's Stripe account
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'pkr',
                'description' => "Order #{$order->id} payment via SOMS Platform",
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
                'publishableKey' => $manufacturer->stripe_publishable_key,
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe PaymentIntent creation failed', [
                'order_id' => $order->id,
                'manufacturer_id' => $order->manufacturer_id,
                'error' => $e->getMessage(),
            ]);
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
        \Stripe\Stripe::setApiKey($order->manufacturer->stripe_secret_key);
        try {
            $paymentIntentId = $request->payment_intent_id;

            Log::info('Stripe PaymentIntent retrieve attempt', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'payment_intent_id' => $paymentIntentId,
            ]);

            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);

            Log::info('Stripe PaymentIntent retrieve succeeded', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'payment_intent_id' => $paymentIntentId,
                'status' => $paymentIntent->status,
            ]);

            if ($paymentIntent->status === 'succeeded') {
                DB::transaction(function () use ($payment, $order) {
                    $payment->update([
                        'status'              => 'completed',
                        'paid_at'             => now(),
                        'gateway_response_code'    => 'succeeded',
                        'gateway_response_message' => 'Stripe PaymentIntent succeeded.',
                    ]);

                    $order->increment('paid_amount', $payment->amount);
                });

                return response()->json(['success' => true]);
            }

            return response()->json(['error' => 'Payment status is: ' . $paymentIntent->status], 400);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            Log::error('Stripe PaymentIntent confirmation failed', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'payment_intent_id' => $request->payment_intent_id,
                'http_status' => $e->getHttpStatus(),
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected Stripe confirmation error', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'payment_intent_id' => $request->payment_intent_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Process Stripe PaymentIntent events forwarded from Stripe.
     */
    public function stripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature', '');
        $webhookSecret = config('services.stripe.webhook_secret');

        if (!$webhookSecret) {
            Log::error('Stripe webhook secret is not configured.');
            return response()->json(['error' => 'Webhook secret not configured.'], 422);
        }

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $signature, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            Log::warning('Stripe webhook payload is invalid.', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload.'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature is invalid.', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature.'], 400);
        }

        if (!in_array($event->type, ['payment_intent.succeeded', 'payment_intent.payment_failed'], true)) {
            return response()->json(['received' => true]);
        }

        $intent = $event->data->object;
        $payment = Payment::where('stripe_payment_intent_id', $intent->id)->first();

        if (!$payment) {
            Log::warning('Stripe webhook payment record not found.', [
                'event_id' => $event->id,
                'payment_intent_id' => $intent->id,
            ]);
            return response()->json(['received' => true]);
        }

        // Use the payee manufacturer's Stripe secret key for API calls
        $payee = $payment->payee;
        if ($payee && !empty($payee->stripe_secret_key)) {
            \Stripe\Stripe::setApiKey($payee->stripe_secret_key);
        }

        if ($event->type === 'payment_intent.succeeded') {
            if ($payment->status !== 'completed') {
                DB::transaction(function () use ($payment) {
                    $payment->update([
                        'status'              => 'completed',
                        'paid_at'             => now(),
                        'gateway_response_code'    => 'succeeded',
                        'gateway_response_message' => 'Stripe PaymentIntent succeeded.',
                    ]);
                    $payment->order->increment('paid_amount', $payment->amount);
                });
            }
        } elseif ($payment->status === 'pending') {
            $payment->update([
                'status'              => 'failed',
                'gateway_response_code'    => $intent->last_payment_error->code ?? 'payment_failed',
                'gateway_response_message' => $intent->last_payment_error->message ?? 'Stripe PaymentIntent failed.',
            ]);
        }

        Log::info('Stripe webhook processed.', [
            'event_id' => $event->id,
            'type' => $event->type,
            'payment_id' => $payment->id,
            'payment_intent_id' => $intent->id,
            'status' => $payment->status,
        ]);

        return response()->json(['received' => true]);
    }

    /**
     * Initiate Safepay payment - creates a tracker and redirects to hosted checkout.
     */
    public function initiateSafepayPayment(Request $request, Order $order)
    {
        if ($order->shop_owner_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$this->safepayService->forManufacturer($order->manufacturer)->isConfigured()) {
            return redirect()->route('shop.orders.show', $order->id)
                ->with('error', 'Safepay is not configured for this manufacturer.');
        }

        $balanceDue = $order->total_amount - $order->paid_amount;

        $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:' . $balanceDue],
        ]);

        try {
            $tracker = $this->safepayService->createTracker(
                (float) $request->amount,
                'PKR',
                ['order_id' => (string) $order->id]
            );

            $authToken = $this->safepayService->createAuthToken();

            $txnRefNo = 'F' . date('ymdHis') . $order->id . strtoupper(Str::random(3));

            Payment::create([
                'order_id'           => $order->id,
                'payer_id'           => Auth::id(),
                'payee_id'           => $order->manufacturer_id,
                'amount'             => $request->amount,
                'txn_ref_no'         => $txnRefNo,
                'safepay_tracker_id' => $tracker,
                'status'             => 'pending',
            ]);

            Log::info('Safepay tracker created for order: ' . $order->id, [
                'tracker'     => $tracker,
                'order_id'    => $order->id,
                'amount'      => $request->amount,
                'environment' => config('services.safepay.environment'),
            ]);

            return redirect()->away(
                $this->safepayService->buildCheckoutUrl(
                    $tracker,
                    $authToken,
                    $order->id
                )
            );
        } catch (\Exception $e) {
            Log::error('Safepay initiation failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);

            return redirect()->route('shop.orders.show', $order->id)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Safepay webhook handler - confirms payment completion.
     */
    public function safepayWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-SFPY-SIGNATURE', '');

        $decoded = json_decode($payload, true);
        if (!is_array($decoded)) {
            return response()->json(['error' => 'Invalid payload.'], 400);
        }

        $data = $decoded['data'] ?? $decoded;
        $tracker = $data['tracker']
            ?? $data['notification']['tracker']
            ?? $data['transaction']['tracker']
            ?? null;

        if (is_array($tracker)) {
            $tracker = $tracker['token'] ?? $tracker['id'] ?? null;
        }

        if (!$tracker) {
            Log::warning('Safepay webhook received without a tracker', ['payload' => $payload]);
            return response()->json(['error' => 'Tracker not found in payload.'], 400);
        }

        $payment = Payment::where('safepay_tracker_id', $tracker)->first();

        if (!$payment) {
            Log::warning('Safepay webhook for unknown tracker', ['tracker' => $tracker]);
            return response()->json(['error' => 'Payment record not found.'], 404);
        }

        // Verify webhook signature using the payee manufacturer's credentials
        $payee = $payment->payee;
        if (!$payee || empty($payee->safepay_webhook_secret)) {
            Log::error('Safepay webhook received but payee manufacturer has no webhook secret configured.', ['payment_id' => $payment->id]);
            return response()->json(['error' => 'Webhook secret not configured.'], 422);
        }

        if (!$this->safepayService->verifyWebhookSignature($payload, $signature, $payee->safepay_webhook_secret)) {
            Log::error('Safepay webhook signature verification failed', [
                'payment_id' => $payment->id,
                'has_signature' => !empty($signature),
                'payload_length' => strlen($payload),
            ]);
            return response()->json(['error' => 'Invalid signature.'], 403);
        }

        if ($payment->status === 'completed') {
            return response()->json(['success' => true]);
        }

        if ($this->isSafepaySuccessEvent($decoded)) {
            DB::transaction(function () use ($payment, $decoded) {
                $order = $payment->order;

                $payment->update([
                    'status'              => 'completed',
                    'paid_at'             => now(),
                    'gateway_response_code'    => data_get($decoded, 'data.response_code', data_get($decoded, 'data.transaction.response_code', '000')),
                    'gateway_response_message' => data_get($decoded, 'data.response_message', data_get($decoded, 'data.transaction.response_message')),
                ]);

                $order->increment('paid_amount', $payment->amount);
            });

            Log::info('Safepay payment completed', [
                'payment_id' => $payment->id,
                'order_id'   => $payment->order_id,
                'tracker'    => $tracker,
            ]);

            return response()->json(['success' => true]);
        }

        Log::info('Safepay webhook received for non-successful event', [
            'tracker' => $tracker,
            'type'    => $decoded['type'] ?? $decoded['data']['type'] ?? null,
        ]);

        return response()->json(['success' => false]);
    }

    private function isSafepaySuccessEvent(array $decoded): bool
    {
        $rootType = $decoded['type'] ?? '';
        if (is_string($rootType) && str_contains(strtolower($rootType), 'succeeded')) {
            return true;
        }

        $data = $decoded['data'] ?? $decoded;
        $type = $data['type'] ?? $data['notification']['type'] ?? '';

        if (is_string($type) && str_contains(strtolower($type), 'succeeded')) {
            return true;
        }

        if (($data['success'] ?? null) === true) {
            return true;
        }

        if (strtoupper($data['state'] ?? '') === 'TRACKER_ENDED') {
            return true;
        }

        if (strtoupper($data['notification']['state'] ?? '') === 'PAID') {
            return true;
        }

        return false;
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

        if ($latestPayment && $latestPayment->status === 'pending' && $this->isStalePending($latestPayment)) {
            $this->expirePendingPayment($latestPayment);
            $latestPayment->refresh();
        }

        return response()->json([
            'status'      => $latestPayment ? $latestPayment->status : null,
            'paid_amount' => $order->paid_amount,
            'balance'     => $order->total_amount - $order->paid_amount,
        ]);
    }

    /**
     * Check if a pending payment is older than 1 minute.
     */
    private function isStalePending(Payment $payment): bool
    {
        return $payment->created_at->diffInSeconds(now()) > 60;
    }

    /**
     * Mark a stale pending payment as failed.
     */
    private function expirePendingPayment(Payment $payment): void
    {
        $payment->update([
            'status'                 => 'failed',
            'gateway_response_code'  => 'TIMEOUT',
            'gateway_response_message' => 'Payment timed out. No confirmation received within 1 minute.',
        ]);

        Log::info('Pending payment auto-expired', [
            'payment_id' => $payment->id,
            'order_id'   => $payment->order_id,
            'created_at' => $payment->created_at->toISOString(),
        ]);
    }

}