<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JazzCashService
{
    /**
     * Process Mobile Wallet Payment
     */
    public function processWalletPayment(
        float $amount,
        string $txnRefNo,
        string $shopOwnerMobile,
        User $manufacturer,
        string $cnic
    ): array {

        $amountInPaisas = round($amount * 100);

        $dateTime = now()->format('YmdHis');
        $expiryDateTime = now()->addDay()->format('YmdHis');

        $payload = [

            'pp_Version'           => '1.1',
            'pp_TxnType'           => 'MWALLET',
            'pp_Language'          => 'EN',

            'pp_MerchantID'        => (string) $manufacturer->jazzcash_merchant_id,
            'pp_SubMerchantID'     => '',
            'pp_Password'          => (string) $manufacturer->jazzcash_password,

            'pp_BankID'            => '',
            'pp_ProductID'         => '',

            'pp_TxnRefNo'          => (string) $txnRefNo,
            'pp_Amount'            => (string) $amountInPaisas,
            'pp_TxnCurrency'       => 'PKR',

            'pp_TxnDateTime'       => $dateTime,
            'pp_BillReference'     => substr($txnRefNo, 0, 20),
            'pp_Description'       => 'Platform facilitated order payment',
            'pp_TxnExpiryDateTime' => $expiryDateTime,

            'pp_ReturnURL'         => config('jazzcash.return_url'),

            'pp_MobileNumber'      => $shopOwnerMobile,
            'pp_CNIC'              => $cnic,

            'pp_DiscountedAmount'  => '',

            'ppmpf_1'              => '',
            'ppmpf_2'              => '',
            'ppmpf_3'              => '',
            'ppmpf_4'              => '',
            'ppmpf_5'              => '',
        ];

        if (empty($payload['pp_ReturnURL'])) {

            Log::error('JazzCash Return URL missing.');

            return [
                'pp_ResponseCode' => '999',
                'pp_ResponseMessage' => 'Return URL not configured'
            ];
        }

        $payload['pp_SecureHash'] = $this->generateSecureHash(
            $payload,
            $manufacturer->jazzcash_integrity_salt
        );

        Log::debug('JazzCash Payload', $payload);

        try {

            $response = Http::asForm()->post(
                config('jazzcash.endpoint_ma'),
                $payload
            );

            Log::debug('JazzCash HTTP Status', [
                'status' => $response->status()
            ]);

            Log::debug('JazzCash Raw Response', [
                'body' => $response->body()
            ]);

            return $response->json() ?? [
                'pp_ResponseCode' => '999',
                'pp_ResponseMessage' => $response->body()
            ];

        } catch (\Throwable $e) {

            Log::error('JazzCash Exception', [
                'message' => $e->getMessage()
            ]);

            return [
                'pp_ResponseCode' => '999',
                'pp_ResponseMessage' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate JazzCash Secure Hash
     */
    public function generateSecureHash(array $payload, string $salt): string
    {
        unset($payload['pp_SecureHash']);

        ksort($payload);

        $hashString = $salt;

        foreach ($payload as $value) {

            if ($value !== null && $value !== '') {
                $hashString .= '&' . $value;
            }
        }

        Log::debug('JazzCash Hash String', [
            'string' => $hashString
        ]);

        $hash = strtoupper(
            hash_hmac('sha256', $hashString, $salt)
        );

        Log::debug('JazzCash SecureHash', [
            'hash' => $hash
        ]);

        dd($hash);
    }
}