<?php
/**
 * I keep getting the same error code 110 for all approaches.
 * 
 * Let me step WAY back and use the JazzCash Hash Calculator from their portal.
 * The portal has a Hash Calculator tool - let's check what fields it expects.
 * 
 * Meanwhile, let me look at the API call log in the portal to see if our requests
 * are even arriving and what hash JazzCash is computing for comparison.
 * 
 * Different approach: Let's try the HTTP POST (page redirect) flow as it uses
 * DIFFERENT API and hash construction from the REST API.
 * 
 * Also let me try calling with much simpler minimal payload that JazzCash SDK uses.
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$mfg = App\Models\User::find(2);
$salt      = $mfg->jazzcash_integrity_salt;
$merchantId = $mfg->jazzcash_merchant_id;
$password   = $mfg->jazzcash_password;

$txnRefNo = 'T' . date('ymdHis') . 'M1';
$dateTime  = date('YmdHis');
$expiry    = date('YmdHis', strtotime('+1 day'));

// Minimal payload - ONLY the fields that appear in a typical working example
$payload = [
    'pp_Version'           => '2.0',
    'pp_TxnType'           => 'MWALLET',
    'pp_Language'          => 'EN',
    'pp_MerchantID'        => $merchantId,
    'pp_Password'          => $password,
    'pp_TxnRefNo'          => $txnRefNo,
    'pp_Amount'            => '100',  // Try small amount: 1 PKR = 100 Paisas
    'pp_TxnCurrency'       => 'PKR',
    'pp_TxnDateTime'       => $dateTime,
    'pp_BillReference'     => 'BILLREF001',
    'pp_Description'       => 'Test',
    'pp_TxnExpiryDateTime' => $expiry,
    'pp_MobileNumber'      => '03123456789',
    'pp_CNIC'              => '345678',
    'pp_ReturnURL'         => 'https://contempt-racism-paralyses.ngrok-free.dev/jazzcash/callback',
];

$f = array_filter($payload, fn($v, $k) => $v !== '' && $v !== null && $k !== 'pp_SecureHash', ARRAY_FILTER_USE_BOTH);
ksort($f);
$s = $salt;
foreach ($f as $v) { $s .= '&' . $v; }
$hash = strtoupper(hash_hmac('sha256', $s, $salt));
$payload['pp_SecureHash'] = $hash;

echo "Pre-image: $s\n\n";
echo "Hash: $hash\n\n";

$endpoint = 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction';
$resp = Http::withHeaders(['Content-Type' => 'application/json'])->post($endpoint, $payload);
$body = $resp->json();
echo "Code: " . ($body['pp_ResponseCode'] ?? 'N/A') . " | Msg: " . ($body['pp_ResponseMessage'] ?? 'N/A') . "\n";
if (isset($body['pp_SecureHash'])) {
    echo "JazzCash Hash: " . $body['pp_SecureHash'] . "\n";
    echo "Our Hash:      " . $hash . "\n";
}
