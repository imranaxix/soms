<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$mfg = App\Models\User::find(2);
$salt      = $mfg->jazzcash_integrity_salt;
$merchantId = $mfg->jazzcash_merchant_id;
$password   = $mfg->jazzcash_password;

$txnRefNo = 'T' . date('ymdHis') . 'A1';
$dateTime  = date('YmdHis');
$expiry    = date('YmdHis', strtotime('+1 hour'));

// No pp_ReturnURL — it is configured in the merchant portal, not the request
$payload = [
    'pp_Version'            => '2.0',
    'pp_TxnType'            => 'MWALLET',
    'pp_Language'           => 'EN',
    'pp_MerchantID'         => $merchantId,
    'pp_Password'           => $password,
    'pp_TxnRefNo'           => $txnRefNo,
    'pp_Amount'             => '1000000',
    'pp_TxnCurrency'        => 'PKR',
    'pp_TxnDateTime'        => $dateTime,
    'pp_BillReference'      => $txnRefNo,
    'pp_Description'        => 'Platform facilitated order payment',
    'pp_TxnExpiryDateTime'  => $expiry,
    'pp_MobileNumber'       => '03123456789',
    'pp_CNIC'               => '345678',
    // pp_ReturnURL intentionally omitted - configured in merchant portal
];

$f = array_filter($payload, fn($v, $k) => $v !== '' && $v !== null && $k !== 'pp_SecureHash', ARRAY_FILTER_USE_BOTH);
ksort($f);
$s = $salt;
foreach ($f as $v) { $s .= '&' . $v; }
$hash = strtoupper(hash_hmac('sha256', $s, $salt));
$payload['pp_SecureHash'] = $hash;

echo "Pre-image: $s\n\n";

$endpoint = 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction';
$resp = Http::withHeaders(['Content-Type' => 'application/json'])->post($endpoint, $payload);
$body = $resp->json();
echo "Code: " . ($body['pp_ResponseCode'] ?? 'N/A') . " | Msg: " . ($body['pp_ResponseMessage'] ?? 'N/A') . "\n";
