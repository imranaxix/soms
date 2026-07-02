<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$mfg = App\Models\User::find(2);
$salt      = $mfg->jazzcash_integrity_salt;
$merchantId = $mfg->jazzcash_merchant_id;
$password   = $mfg->jazzcash_password;

$txnRefNo = 'T' . date('ymdHis') . 'X'; // 16 characters (T + 14 + 1)
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
    'pp_BillReference'      => substr($txnRefNo, 0, 20),
    'pp_Description'        => 'Platform facilitated order payment',
    'pp_TxnExpiryDateTime'  => $expiry,
    'pp_MobileNumber'       => '03123456789',
    'pp_CNIC'               => '345678',
    'pp_ReturnURL'          => 'https://contempt-racism-paralyses.ngrok-free.dev/jazzcash/callback',
];

$strategies = [
    'Standard (Exclude Empty)' => function($p) {
        return array_filter($p, fn($v, $k) => $v !== '' && $v !== null && $k !== 'pp_SecureHash', ARRAY_FILTER_USE_BOTH);
    },
    'Keep Empty' => function($p) {
        return array_filter($p, fn($v, $k) => $v !== null && $k !== 'pp_SecureHash', ARRAY_FILTER_USE_BOTH);
    },
    'Exclude Password' => function($p) {
        return array_filter($p, fn($v, $k) => $v !== '' && $v !== null && $k !== 'pp_SecureHash' && $k !== 'pp_Password', ARRAY_FILTER_USE_BOTH);
    },
    'Exclude ReturnURL' => function($p) {
        return array_filter($p, fn($v, $k) => $v !== '' && $v !== null && $k !== 'pp_SecureHash' && $k !== 'pp_ReturnURL', ARRAY_FILTER_USE_BOTH);
    },
    'Exclude Description' => function($p) {
        return array_filter($p, fn($v, $k) => $v !== '' && $v !== null && $k !== 'pp_SecureHash' && $k !== 'pp_Description', ARRAY_FILTER_USE_BOTH);
    },
    'Exclude Description & ReturnURL' => function($p) {
        return array_filter($p, fn($v, $k) => $v !== '' && $v !== null && $k !== 'pp_SecureHash' && $k !== 'pp_Description' && $k !== 'pp_ReturnURL', ARRAY_FILTER_USE_BOTH);
    },
    'Exclude Password & ReturnURL' => function($p) {
        return array_filter($p, fn($v, $k) => $v !== '' && $v !== null && $k !== 'pp_SecureHash' && $k !== 'pp_Password' && $k !== 'pp_ReturnURL', ARRAY_FILTER_USE_BOTH);
    }
];

$endpoint = 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction';

foreach ($strategies as $name => $strategy) {
    $filtered = $strategy($payload);
    ksort($filtered);
    $hashString = $salt;
    foreach ($filtered as $value) {
        $hashString .= '&' . $value;
    }
    $p = $payload;
    $p['pp_SecureHash'] = strtoupper(hash_hmac('sha256', $hashString, $salt));
    
    $resp = Http::withHeaders(['Content-Type' => 'application/json'])->post($endpoint, $p);
    echo "[$name] Code: " . $resp->json('pp_ResponseCode') . " | Msg: " . $resp->json('pp_ResponseMessage') . "\n";
    if ($resp->json('pp_ResponseCode') === '000' || $resp->json('pp_ResponseMessage') !== 'Please provide valid value for pp_SecureHash.') {
        echo "FOUND MATCH OR DIFFERENT ERROR: $name\n";
    }
}
