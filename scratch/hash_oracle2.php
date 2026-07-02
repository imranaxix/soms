<?php
/**
 * The oracle test proved our hash algorithm works CORRECTLY for the response.
 * 
 * Oracle test from hash_oracle.php:
 * - Response payload (excluding pp_SecureHash, removing empties) → sorted → salt+values
 * - Hash: EA96E1EE... = JazzCash's own hash EA96E1EE... ✓ MATCHES!
 * 
 * So why does JazzCash reject our request hash?
 * 
 * KEY INSIGHT: JazzCash hashes the REQUEST payload to validate it.
 * But the RESPONSE payload omits pp_ReturnURL!
 * 
 * So: JazzCash does NOT include pp_ReturnURL in its hash calculation for requests!
 * That's why our request hash (which includes pp_ReturnURL) doesn't match!
 * 
 * Let's verify: what does the request payload look like WITHOUT pp_ReturnURL for hash,
 * but STILL sends it in the POST body?
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$mfg = App\Models\User::find(2);
$salt      = $mfg->jazzcash_integrity_salt;
$merchantId = $mfg->jazzcash_merchant_id;
$password   = $mfg->jazzcash_password;

$txnRefNo = 'T' . date('ymdHis') . 'Y1';
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
    'pp_CNIC'               => '345678',
    'pp_ReturnURL'          => 'https://contempt-racism-paralyses.ngrok-free.dev/jazzcash/callback',
];

// Strategy: Hash WITHOUT pp_ReturnURL (like the response doesn't have it)
$forHash = $payload;
unset($forHash['pp_ReturnURL']);
$forHash = array_filter($forHash, fn($v) => $v !== '' && $v !== null);
ksort($forHash);
$hashString = $salt;
foreach ($forHash as $value) {
    $hashString .= '&' . $value;
}
$hash = strtoupper(hash_hmac('sha256', $hashString, $salt));
$payload['pp_SecureHash'] = $hash;

echo "Pre-image (no ReturnURL): " . $hashString . "\n";
echo "Hash: " . $hash . "\n\n";

$endpoint = 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction';
$resp = Http::withHeaders(['Content-Type' => 'application/json'])->post($endpoint, $payload);
echo "Code: " . $resp->json('pp_ResponseCode') . " | Msg: " . $resp->json('pp_ResponseMessage') . "\n";
