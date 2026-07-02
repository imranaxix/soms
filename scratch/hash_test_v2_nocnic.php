<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$mfg = App\Models\User::find(2);
$salt      = $mfg->jazzcash_integrity_salt;
$merchantId = $mfg->jazzcash_merchant_id;
$password   = $mfg->jazzcash_password;

$txnRefNo = 'T' . date('ymdHis') . 'V2N';
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
    'pp_ReturnURL'          => 'https://contempt-racism-paralyses.ngrok-free.dev/jazzcash/callback',
];

$filtered = array_filter($payload, function ($value, $key) {
    return $key !== 'pp_SecureHash' && $value !== '' && $value !== null;
}, ARRAY_FILTER_USE_BOTH);

ksort($filtered);
$hashString = $salt;
foreach ($filtered as $value) {
    $hashString .= '&' . $value;
}
$payload['pp_SecureHash'] = strtoupper(hash_hmac('sha256', $hashString, $salt));

$endpoint = 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction';
$resp = Http::withHeaders(['Content-Type' => 'application/json'])->post($endpoint, $payload);
echo "v2.0 NO CNIC test result:\n";
echo "Code: " . $resp->json('pp_ResponseCode') . " | Msg: " . $resp->json('pp_ResponseMessage') . "\n";
print_r($resp->json());
