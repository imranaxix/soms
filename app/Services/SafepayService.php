<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Safepay\Checkout;
use Safepay\SafepayClient;
use Safepay\Webhook;

class SafepayService
{
    protected ?User $manufacturer = null;

    /**
     * Set the manufacturer whose credentials to use.
     */
    public function forManufacturer(User $manufacturer): self
    {
        $this->manufacturer = $manufacturer;
        return $this;
    }

    /**
     * Whether the given manufacturer has Safepay credentials configured.
     */
    public function isConfigured(?User $manufacturer = null): bool
    {
        $manufacturer ??= $this->manufacturer;

        if (!$manufacturer || !$manufacturer->hasSafepay()) {
            return false;
        }

        $secretKey = $manufacturer->safepay_secret_key;
        $webhookSecret = $manufacturer->safepay_webhook_secret;

        if (!empty($secretKey) && !empty($webhookSecret) && $secretKey === $webhookSecret) {
            Log::warning('Safepay config mismatch for manufacturer ' . $manufacturer->id . ': secret_key and webhook_secret are identical.');
            return false;
        }

        return true;
    }

    /**
     * Create a Safepay payment tracker.
     *
     * Amounts are converted from rupees to the currency's minor unit expected
     * by Safepay's Express Checkout API.
    */
    public function createTracker(float $amount, string $currency, array $metadata = []): string
    {
        try {
            $session = $this->safepay()->order->setup([
                'merchant_api_key' => $this->publicKey(),
                // MPGS was tested as an alternate intent and reproduced the same authorization failure.
                'intent'           => 'CYBERSOURCE',
                'mode'             => 'payment',
                'entry_mode'       => 'raw',
                'currency'         => $currency,
                'amount'           => (int) round($amount * 100),
                'metadata'         => $metadata,
            ]);
        } catch (\Exception $e) {
            Log::error('Safepay tracker creation failed', [
                'amount'   => $amount,
                'currency' => $currency,
                'error'    => $e->getMessage(),
            ]);

            throw new \RuntimeException('Safepay could not create a payment session. Please try again.');
        }

        $shape = $session->toArray();
        Log::info('Safepay order setup response shape', [
            'keys'     => $session->keys(),
            'response' => $this->redactTokens($shape),
        ]);

        $tracker = $session->tracker->token ?? null;

        if (!$tracker) {
            Log::error('Safepay tracker response invalid', ['response' => $this->redactTokens($shape)]);

            throw new \RuntimeException('Safepay returned an invalid payment session response.');
        }

        Log::info('Safepay Tracker Token Created', [
            'tracker'     => $tracker,
            'environment' => $this->environment(),
            'client'      => $this->publicKey(),
        ]);

        return $tracker;
    }

    /**
     * Create the short-lived token required by Express Checkout.
     */
    public function createAuthToken(): string
    {
        try {
            $passport = $this->safepay()->passport->create();
        } catch (\Exception $e) {
            Log::error('Safepay authorization token creation failed', [
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Safepay could not authorize the checkout session. Please try again.');
        }

        $shape = $passport->toArray();
        Log::info('Safepay passport response shape', [
            'keys'     => $passport->keys(),
            'response' => $this->redactTokens($shape),
        ]);

        if (!$passport->token) {
            Log::error('Safepay passport response invalid', ['response' => $this->redactTokens($shape)]);

            throw new \RuntimeException('Safepay returned an invalid authorization token.');
        }

        return $passport->token;
    }

    /**
     * Build the current Express Checkout URL.
     */
    public function buildCheckoutUrl(string $tracker, string $authToken, int $orderId): string
    {
        try {
            $returnUrl = route('shop.orders.show', ['id' => $orderId]);
            $url = Checkout::constructURL([
                'environment' => $this->environment(),
                'tracker'     => $tracker,
                'tbt'         => $authToken,
                'source'      => 'hosted',
                'redirect_url' => $returnUrl,
                'cancel_url'   => $returnUrl,
            ]);
        } catch (\Exception $e) {
            Log::error('Safepay checkout URL generation failed', [
                'tracker' => $tracker,
                'order_id' => $orderId,
                'error'    => $e->getMessage(),
            ]);

            throw new \RuntimeException('Safepay could not generate a checkout URL.');
        }

        Log::info('Safepay Generated Checkout URL', [
            'url' => preg_replace('/([?&]tbt=)[^&]*/', '$1[redacted]', $url),
        ]);

        return $url;
    }

    /**
     * Verify the authenticity of an incoming Safepay webhook.
     *
     * Safepay webhooks are signed with HMAC-SHA512 over the raw request body.
     */
    public function verifyWebhookSignature(string $payload, string $signature, string $webhookSecret): bool
    {
        try {
            Webhook::constructEvent($payload, $signature, $webhookSecret);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function safepay(): SafepayClient
    {
        return new SafepayClient([
            'api_key'  => $this->secretKey(),
            'api_base' => $this->apiBaseUrl(),
        ]);
    }

    private function redactTokens(array $values): array
    {
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $values[$key] = $this->redactTokens($value);
            } elseif ($key === 'token') {
                $values[$key] = '[redacted]';
            }
        }

        return $values;
    }

    private function environment(): string
    {
        return $this->manufacturer?->safepay_environment ?? config('services.safepay.environment', 'sandbox');
    }

    private function publicKey(): string
    {
        return (string) ($this->manufacturer?->safepay_api_key ?? config('services.safepay.public_key'));
    }

    private function secretKey(): string
    {
        return (string) ($this->manufacturer?->safepay_secret_key ?? config('services.safepay.secret_key'));
    }

    private function apiBaseUrl(): string
    {
        return $this->environment() === 'sandbox'
            ? 'https://sandbox.api.getsafepay.com'
            : 'https://api.getsafepay.com';
    }
}