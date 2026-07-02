<?php
// We need to figure out what hash JazzCash is validating against our request.
// The response pp_SecureHash is JazzCash's OWN signature — we can't reverse-engineer from it.
// So instead: let's make a REAL test request with a KNOWN hash to sandbox and see if it accepts it.
// First, let me try many more hash construction strategies.

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$user = App\Models\User::find(2);
$salt      = $user->jazzcash_integrity_salt;
$merchantId = $user->jazzcash_merchant_id;
$password   = $user->jazzcash_password;

echo "Salt:        $salt\n";
echo "MerchantID:  $merchantId\n";
echo "Password:    $password\n\n";

$txnRefNo = 'T' . date('ymdHis') . '4TST';
$dateTime  = date('YmdHis');
$expiry    = date('YmdHis', strtotime('+1 day'));

$payload = [
    'pp_Version'           => '2.0',
    'pp_TxnType'           => 'MWALLET',
    'pp_Language'          => 'EN',
    'pp_MerchantID'        => $merchantId,
    'pp_Password'          => $password,
    'pp_TxnRefNo'          => $txnRefNo,
    'pp_Amount'            => '100000',  // Rs 1000
    'pp_TxnCurrency'       => 'PKR',
    'pp_TxnDateTime'       => $dateTime,
    'pp_BillReference'     => $txnRefNo,
    'pp_Description'       => 'Test payment',
    'pp_TxnExpiryDateTime' => $expiry,
    'pp_MobileNumber'      => '03001234567',
    'pp_CNIC'              => '345678',
    'pp_ReturnURL'         => 'http://localhost:8000/jazzcash/callback',
];

function buildHash(array $fields, string $salt, array $exclude = [], bool $includeEmpty = false, bool $withSalt = true): string {
    $f = $fields;
    foreach ($exclude as $k) unset($f[$k]);
    if (!$includeEmpty) {
        $f = array_filter($f, fn($v) => $v !== '' && $v !== null);
    }
    ksort($f);
    $str = $withSalt ? $salt : '';
    foreach ($f as $v) {
        $str .= ($str === '' ? '' : '&') . $v;
    }
    return strtoupper(hash_hmac('sha256', $str, $salt));
}

// Try sending with each variation and see which one returns pp_ResponseCode != 110 for SecureHash
$variations = [
    'All fields'                        => buildHash($payload, $salt),
    'Without pp_Password'               => buildHash($payload, $salt, ['pp_Password']),
    'Without pp_ReturnURL'              => buildHash($payload, $salt, ['pp_ReturnURL']),
    'Without pp_Description'            => buildHash($payload, $salt, ['pp_Description']),
    'Without pp_Password + ReturnURL'   => buildHash($payload, $salt, ['pp_Password', 'pp_ReturnURL']),
    'No salt in string'                 => buildHash($payload, $salt, [], false, false),
    'Without pp_Version'                => buildHash($payload, $salt, ['pp_Version']),
    'Without pp_Language'               => buildHash($payload, $salt, ['pp_Language']),
    'Without pp_CNIC'                   => buildHash($payload, $salt, ['pp_CNIC']),
    'Only: MID,Pwd,TxnRef,Amt,Ccy,Dt,Bill,Mobile,CNIC,Exp' => buildHash([
        'pp_MerchantID'        => $merchantId,
        'pp_Password'          => $password,
        'pp_TxnRefNo'          => $txnRefNo,
        'pp_Amount'            => '100000',
        'pp_TxnCurrency'       => 'PKR',
        'pp_TxnDateTime'       => $dateTime,
        'pp_BillReference'     => $txnRefNo,
        'pp_MobileNumber'      => '03001234567',
        'pp_CNIC'              => '345678',
        'pp_TxnExpiryDateTime' => $expiry,
    ], $salt),
];

$endpoint = 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction';

echo "Testing " . count($variations) . " hash variations against live sandbox...\n\n";

foreach ($variations as $label => $hash) {
    $p = $payload;
    $p['pp_SecureHash'] = $hash;

    try {
        $resp = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->timeout(10)
                    ->post($endpoint, $p);
        $data = $resp->json();
        $code = $data['pp_ResponseCode'] ?? '???';
        $msg  = $data['pp_ResponseMessage'] ?? '???';
        $hashMatch = ($code !== '110' || strpos($msg, 'SecureHash') === false) ? '✅ PASSED HASH' : '❌ Bad hash';
        echo "[$hashMatch] $label\n";
        echo "  Hash: $hash\n";
        echo "  Code: $code | Msg: $msg\n\n";

        if ($hashMatch !== '❌ Bad hash') {
            echo "==== WINNER FOUND: $label ====\n";
            break;
        }
    } catch (\Exception $e) {
        echo "  ERROR: " . $e->getMessage() . "\n";
    }
}
