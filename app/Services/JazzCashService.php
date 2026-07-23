<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class JazzCashService
{
    // Hardcoded sandbox credentials (for testing only)
    private const MERCHANT_ID    = 'MC838138';
    private const PASSWORD       = 'a62159yb5y';
    private const INTEGRITY_SALT = '951zt9xe85';

    /**
     * Build payload for HTTP POST Page Redirect method (v1.1).
     */
    public function buildRedirectPayload(
        float $amount,
        string $txnRefNo,
        string $shopOwnerMobile,
        User $manufacturer,
        string $cnic
    ): array {
        $amountInPaisas = (string) round($amount * 100);
        $txnDateTime    = now()->format('YmdHis');
        $expiryDateTime = now()->addHour()->format('YmdHis');

        $payload = [
            'pp_Amount'            => $amountInPaisas,
            // Ensure BillReference is <= 20 chars and different from TxnRefNo
            'pp_BillReference'     => 'B' . substr($txnRefNo, 1), 
            'pp_Description'       => 'Platform facilitated order payment',
            'pp_Language'          => 'EN',
            'pp_MerchantID'        => self::MERCHANT_ID,
            'pp_Password'          => self::PASSWORD,
            'pp_ReturnURL'         => config('jazzcash.return_url'),
            'pp_SubMerchantID'     => '',
            'pp_TxnCurrency'       => 'PKR',
            'pp_TxnDateTime'       => $txnDateTime,
            'pp_TxnExpiryDateTime' => $expiryDateTime,
            'pp_TxnRefNo'          => $txnRefNo,
            'pp_TxnType'           => '',
            'pp_Version'           => '1.1',
            'ppmpf_1'              => '',
            'ppmpf_2'              => '',
            'ppmpf_3'              => '',
            'ppmpf_4'              => '',
            'ppmpf_5'              => '',
        ];

        $payload['pp_SecureHash'] = $this->generateSecureHash($payload, self::INTEGRITY_SALT);

        Log::debug('JazzCash Redirect Payload Built', $payload);

        return $payload;
    }

    /**
     * Generate HMAC-SHA256 secure hash matching the JazzCash Hash Calculator exactly.
     */
    public function generateSecureHash(array $payload, string $salt): string
    {
        unset($payload['pp_SecureHash']);

        ksort($payload);

        $finalString = $salt . '&';

        foreach ($payload as $value) {
            $value = (string) $value;
            $finalString .= $value;
            if ($value !== '') {
                $finalString .= '&';
            }
        }

        $finalString = rtrim($finalString, '&');

        Log::debug('JazzCash Hash String', ['string' => $finalString]);

        return strtoupper(hash_hmac('sha256', $finalString, $salt));
    }
}