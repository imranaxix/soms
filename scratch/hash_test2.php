<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$user = App\Models\User::find(2);
$salt      = $user->jazzcash_integrity_salt;
$merchantId = $user->jazzcash_merchant_id;
$password   = $user->jazzcash_password;

$txnRefNo = 'T2607020548384LRQ';
$dateTime  = '20260702054838';
$expiry    = '20260703054838';

$payload = [
    'pp_Version'           => '2.0',
    'pp_TxnType'           => 'MWALLET',
    'pp_Language'          => 'EN',
    'pp_MerchantID'        => $merchantId,
    'pp_Password'          => $password,
    'pp_TxnRefNo'          => $txnRefNo,
    'pp_Amount'            => '1000000',
    'pp_TxnCurrency'       => 'PKR',
    'pp_TxnDateTime'       => $dateTime,
    'pp_BillReference'     => $txnRefNo,
    'pp_Description'       => 'Platform facilitated order payment',
    'pp_TxnExpiryDateTime' => $expiry,
    'pp_MobileNumber'      => '03001234567',
    'pp_CNIC'              => '345678',
    'pp_ReturnURL'         => 'http://localhost:8000/jazzcash/callback',
];

function buildHash(array $fields, string $salt, array $exclude = []): string {
    $f = $fields;
    foreach ($exclude as $k) unset($f[$k]);
    $f = array_filter($f, fn($v) => $v !== '' && $v !== null);
    ksort($f);
    $str = $salt;
    foreach ($f as $v) {
        $str .= '&' . $v;
    }
    return strtoupper(hash_hmac('sha256', $str, $salt));
}

$endpoint = 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction';

$variations = [
    'Without pp_Password' => buildHash($payload, $salt, ['pp_Password']),
    'Without pp_Password (No Spaces in Desc)' => buildHash(array_merge($payload, ['pp_Description' => 'OrderPayment']), $salt, ['pp_Password']),
];

foreach ($variations as $label => $hash) {
    $p = $payload;
    if ($label === 'Without pp_Password (No Spaces in Desc)') {
        $p['pp_Description'] = 'OrderPayment';
    }
    $p['pp_SecureHash'] = $hash;
    $resp = Http::withHeaders(['Content-Type' => 'application/json'])->post($endpoint, $p);
    echo "[$label]\n Code: " . $resp->json('pp_ResponseCode') . " | Msg: " . $resp->json('pp_ResponseMessage') . "\n";
}
