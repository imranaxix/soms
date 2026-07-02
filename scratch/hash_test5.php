<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$user = App\Models\User::find(2);
$salt      = $user->jazzcash_integrity_salt;
$merchantId = $user->jazzcash_merchant_id;
$password   = $user->jazzcash_password;

$txnRefNo = 'T' . date('ymdHis') . 'TEST';
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
    'pp_MobileNumber'       => '03001234567',
    'pp_CNIC'               => '345678',
    'pp_ReturnURL'          => 'http://localhost:8000/jazzcash/callback',
];

$filtered = array_filter($payload, function ($value, $key) {
    return $key !== 'pp_SecureHash'
        && $key !== 'pp_Password'
        && $value !== ''
        && $value !== null;
}, ARRAY_FILTER_USE_BOTH);
ksort($filtered);
$hashString = $salt;
foreach ($filtered as $value) {
    $hashString .= '&' . $value;
}
$payload['pp_SecureHash'] = strtoupper(hash_hmac('sha256', $hashString, $salt));

$endpoint = 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction';
$resp = Http::withHeaders(['Content-Type' => 'application/json'])->post($endpoint, $payload);
echo "Code: " . $resp->json('pp_ResponseCode') . " | Msg: " . $resp->json('pp_ResponseMessage') . "\n";
