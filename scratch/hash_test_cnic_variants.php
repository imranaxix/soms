<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$mfg = App\Models\User::find(2);
$salt      = $mfg->jazzcash_integrity_salt;
$merchantId = $mfg->jazzcash_merchant_id;
$password   = $mfg->jazzcash_password;

$endpoint = 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction';

$cnicKeys = [
    'pp_CNIC',
    'pp_Cnic',
    'pp_cnic',
    'pp_CustomerCNIC',
    'pp_CustomerCnic',
    'pp_Customercnic',
    'pp_CNICLast6',
    'pp_CnicLast6'
];

$i = 0;
foreach ($cnicKeys as $keyName) {
    $i++;
    $txnRefNo = 'T' . date('ymdHis') . 'C' . $i;
    $dateTime  = date('YmdHis');
    $expiry    = date('YmdHis', strtotime('+1 day'));

    $payload = [
        'pp_Version'            => '2.0',
        'pp_TxnType'            => 'MWALLET',
        'pp_Language'           => 'EN',
        'pp_MerchantID'         => (string) $merchantId,
        'pp_Password'           => (string) $password,
        'pp_TxnRefNo'           => (string) $txnRefNo,
        'pp_Amount'             => '1000000',
        'pp_TxnCurrency'        => 'PKR',
        'pp_TxnDateTime'        => $dateTime,
        'pp_BillReference'      => $txnRefNo,
        'pp_Description'        => 'Platform facilitated order payment',
        'pp_TxnExpiryDateTime'  => $expiry,
        'pp_MobileNumber'       => '03123456789',
        $keyName                => '345678',
        'pp_ReturnURL'          => 'https://contempt-racism-paralyses.ngrok-free.dev/jazzcash/callback',
    ];

    // Case 1: Key included in hash calculation
    $filtered1 = array_filter($payload, function ($value, $key) {
        return $key !== 'pp_SecureHash' && $value !== '' && $value !== null;
    }, ARRAY_FILTER_USE_BOTH);
    ksort($filtered1);
    $hashString1 = $salt;
    foreach ($filtered1 as $value) {
        $hashString1 .= '&' . $value;
    }
    $hash1 = strtoupper(hash_hmac('sha256', $hashString1, $salt));
    $payload['pp_SecureHash'] = $hash1;

    try {
        $resp1 = Http::withHeaders(['Content-Type' => 'application/json'])->post($endpoint, $payload);
        echo "[$keyName - Hashed] Code: " . $resp1->json('pp_ResponseCode') . " | Msg: " . $resp1->json('pp_ResponseMessage') . "\n";
    } catch (\Exception $e) {
        echo "[$keyName - Hashed] Failed: " . $e->getMessage() . "\n";
    }

    // Case 2: Key excluded from hash calculation
    $filtered2 = array_filter($payload, function ($value, $key) use ($keyName) {
        return $key !== 'pp_SecureHash' && $key !== $keyName && $value !== '' && $value !== null;
    }, ARRAY_FILTER_USE_BOTH);
    ksort($filtered2);
    $hashString2 = $salt;
    foreach ($filtered2 as $value) {
        $hashString2 .= '&' . $value;
    }
    $hash2 = strtoupper(hash_hmac('sha256', $hashString2, $salt));
    $payload['pp_SecureHash'] = $hash2;

    try {
        $resp2 = Http::withHeaders(['Content-Type' => 'application/json'])->post($endpoint, $payload);
        echo "[$keyName - Not Hashed] Code: " . $resp2->json('pp_ResponseCode') . " | Msg: " . $resp2->json('pp_ResponseMessage') . "\n";
    } catch (\Exception $e) {
        echo "[$keyName - Not Hashed] Failed: " . $e->getMessage() . "\n";
    }

    usleep(150000);
}
