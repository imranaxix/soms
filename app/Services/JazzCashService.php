<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class JazzCashService
{
    /**
     * Build payload for HTTP POST Page Redirect method (v1.1)
     * This follows JazzCash's documented HTTP POST method for MWALLET
     */
    public function buildRedirectPayload(
        float $amount,
        string $txnRefNo,
        string $shopOwnerMobile,
        User $manufacturer,
        string $cnic
    ): array {
        // Amount in paisas (multiply by 100 and cast to string)
        $amountInPaisas = (string) round($amount * 100);
        $txnDateTime = now()->format('YmdHis');
        $expiryDateTime = now()->addHour()->format('YmdHis'); // Expiry reduced to 1 hour for secure checkout

        // Complete base payload including MWALLET credentials
        $payload = [
            'pp_Version'            => '1.1',
            'pp_TxnType'            => 'MWALLET',
            'pp_Language'           => 'EN',
            'pp_MerchantID'        => $manufacturer->jazzcash_merchant_id,
            'pp_Password'          => $manufacturer->jazzcash_password,
            'pp_TxnRefNo'          => $txnRefNo,
            'pp_Amount'            => $amountInPaisas,
            'pp_TxnCurrency'       => 'PKR',
            'pp_TxnDateTime'       => $txnDateTime,
            'pp_TxnExpiryDateTime' => $expiryDateTime,
            'pp_BillReference'     => substr($txnRefNo, 0, 20),
            'pp_Description'       => 'Platform facilitated order payment',
            'pp_ReturnURL'         => config('jazzcash.return_url'),
            
            // Mobile Wallet parameters are required to be included in the dynamic hash
            'pp_MobileNumber'      => $shopOwnerMobile,
            'pp_CNIC'              => $cnic,
        ];

        // Generate dynamic secure hash across all active inputs 
        $payload['pp_SecureHash'] = $this->generateSecureHash(
            $payload,
            $manufacturer->jazzcash_integrity_salt
        );

        // Append remaining empty gateway properties AFTER hash generation
        $payload['pp_BankID'] = '';
        $payload['pp_ProductID'] = '';
        $payload['ppmpf_1'] = '';
        $payload['ppmpf_2'] = '';
        $payload['ppmpf_3'] = '';
        $payload['ppmpf_4'] = '';
        $payload['ppmpf_5'] = '';

        Log::debug('JazzCash Redirect Payload Built (v1.1)', $payload);

        return $payload;
    }

    /**
     * Generate secure hash for JazzCash v1.1 HTTP POST (Page Redirect)
     */
    public function generateSecureHash(array $payload, string $salt): string
    {
        // 1. Remove the secure hash parameter if it's already in the array
        unset($payload['pp_SecureHash']);

        // 2. Exclude any keys that have a value of null or an empty string ""
        $filtered = array_filter($payload, function ($value) {
            return $value !== null && $value !== '';
        });

        // 3. Sort all remaining payload keys alphabetically (A-Z)
        ksort($filtered);

        // 4. Build string chain: Start with the Integrity Salt, followed by an ampersand, then join values
        $hashString = $salt . '&' . implode('&', $filtered);

        Log::debug('Generated JazzCash String Chain (Dynamic Flow)', [
            'string' => $hashString,
            'filtered_keys' => array_keys($filtered)
        ]);

        // 5. Generate the HMAC SHA-256 signature using the salt as the secret key
        return strtoupper(
            hash_hmac('sha256', $hashString, $salt)
        );
    }
}