<?php
/**
 * Let me check the AticMatic library more carefully.
 * The initiateMobileWalletPayment() doesn't include pp_MerchantID, pp_Password,
 * pp_Version, pp_TxnType, pp_Language, pp_TxnRefNo in $payload before hashing.
 * But generateRequestHash ONLY takes what's in $data.
 * 
 * So either:
 * 1. generateRequestHash adds those fields before hashing, OR  
 * 2. They are added AFTER hashing (so hash only covers Amount, Currency, DateTime etc.)
 * 
 * Looking at the code: generateRequestHash returns a STRING (the hash), not an array.
 * But it's called as: $payload = $this->generateRequestHash($payload);
 * That would replace $payload with a string! That can't be right...
 * 
 * Wait - let me re-read: "return strtoupper(hash_hmac(...))"
 * And it's used as: $payload = $this->generateRequestHash($payload);
 * 
 * Hmm, that makes $payload = hash_string. That seems wrong.
 * Unless the function returns the whole array with hash added.
 * 
 * But the return type says `string`. Let me check if there's another version.
 * 
 * Actually, looking at the source again: the return type says `string` but maybe
 * the actual $payload gets modified in-place? No...
 * 
 * OR maybe makeApiCall also adds pp_MerchantID, pp_Password etc.
 * 
 * The key insight: the hash is computed ONLY from the data fields that are in the
 * initiateMobileWalletPayment payload - and those DON'T include pp_MerchantID,
 * pp_Password, pp_Version, pp_TxnType, pp_Language, pp_TxnRefNo!
 * 
 * Let me test this: hash ONLY Amount, CNIC, TxnCurrency, TxnDateTime, TxnExpiryDateTime,
 * BillReference, Description, MobileNumber
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$mfg = App\Models\User::find(2);
$salt      = $mfg->jazzcash_integrity_salt;
$merchantId = $mfg->jazzcash_merchant_id;
$password   = $mfg->jazzcash_password;

$txnRefNo = 'T' . date('ymdHis') . 'B2';
$dateTime  = date('YmdHis');
$expiry    = date('YmdHis', strtotime('+1 hour'));

// HASH-ONLY fields (no auth fields, no ReturnURL)
$hashFields = [
    'pp_Amount'             => '1000000',
    'pp_BillReference'      => $txnRefNo,
    'pp_CNIC'               => '345678',
    'pp_Description'        => 'Platform facilitated order payment',
    'pp_MobileNumber'       => '03123456789',
    'pp_TxnCurrency'        => 'PKR',
    'pp_TxnDateTime'        => $dateTime,
    'pp_TxnExpiryDateTime'  => $expiry,
];

// Compute hash from only transaction fields
$f = array_filter($hashFields, fn($v) => $v !== '' && $v !== null);
ksort($f);
$s = $salt;
foreach ($f as $v) { $s .= '&' . $v; }
$hash = strtoupper(hash_hmac('sha256', $s, $salt));

echo "Hash-only pre-image: $s\n\n";

// Full payload for the API call (auth fields added after hash is computed)
$payload = array_merge($hashFields, [
    'pp_Version'            => '2.0',
    'pp_TxnType'            => 'MWALLET',
    'pp_Language'           => 'EN',
    'pp_MerchantID'         => $merchantId,
    'pp_Password'           => $password,
    'pp_TxnRefNo'           => $txnRefNo,
    'pp_SecureHash'         => $hash,
]);

$endpoint = 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction';
$resp = Http::withHeaders(['Content-Type' => 'application/json'])->post($endpoint, $payload);
$body = $resp->json();
echo "Code: " . ($body['pp_ResponseCode'] ?? 'N/A') . " | Msg: " . ($body['pp_ResponseMessage'] ?? 'N/A') . "\n";
