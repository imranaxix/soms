<?php
/**
 * Let's look at it from a totally different angle.
 * 
 * In hash_oracle.php we established:
 * - Our algorithm reproduces the JazzCash RESPONSE hash exactly
 * - The response includes: pp_TxnType, pp_Version, pp_Amount, pp_AuthCode(empty skip), 
 *   pp_BillReference, pp_Language, pp_MerchantID, pp_ResponseCode, pp_ResponseMessage,
 *   pp_RetreivalReferenceNo(empty skip), pp_SubMerchantID(empty skip), pp_TxnCurrency,
 *   pp_TxnDateTime, pp_TxnRefNo, pp_MobileNumber, pp_CNIC, pp_DiscountedAmount(empty skip)
 *   ppmpf_1..5 (empty skip)
 * 
 * But the REQUEST includes pp_Password and pp_ReturnURL!
 * 
 * The response DOESN'T include pp_Password or pp_ReturnURL.
 * 
 * JazzCash validates our hash by re-hashing what THEY RECEIVED, but they might strip
 * pp_Password before hashing (since that's authentication, not data signing).
 * 
 * So for request hash: EXCLUDE pp_Password, INCLUDE pp_ReturnURL
 * Let's test that!
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$mfg = App\Models\User::find(2);
$salt      = $mfg->jazzcash_integrity_salt;
$merchantId = $mfg->jazzcash_merchant_id;
$password   = $mfg->jazzcash_password;

$txnRefNo = 'T' . date('ymdHis') . 'Z1';
$dateTime  = date('YmdHis');
$expiry    = date('YmdHis', strtotime('+1 day'));

$tests = [
    // Test 1: Exclude pp_Password, Include pp_ReturnURL
    'Exclude Password, Include ReturnURL' => function($payload, $salt) {
        $f = array_filter($payload, fn($v, $k) => $v !== '' && $v !== null && $k !== 'pp_Password' && $k !== 'pp_SecureHash', ARRAY_FILTER_USE_BOTH);
        ksort($f);
        $s = $salt;
        foreach ($f as $v) { $s .= '&' . $v; }
        return strtoupper(hash_hmac('sha256', $s, $salt));
    },
    // Test 2: Include all including password
    'Include All (no empties)' => function($payload, $salt) {
        $f = array_filter($payload, fn($v, $k) => $v !== '' && $v !== null && $k !== 'pp_SecureHash', ARRAY_FILTER_USE_BOTH);
        ksort($f);
        $s = $salt;
        foreach ($f as $v) { $s .= '&' . $v; }
        return strtoupper(hash_hmac('sha256', $s, $salt));
    },
    // Test 3: Response-style fields only (subset matching response)
    'Response-style subset' => function($payload, $salt) {
        $responseKeys = ['pp_Amount','pp_BillReference','pp_CNIC','pp_Language','pp_MerchantID',
                         'pp_MobileNumber','pp_TxnCurrency','pp_TxnDateTime','pp_TxnRefNo','pp_TxnType','pp_Version'];
        $f = array_filter($payload, fn($v, $k) => in_array($k, $responseKeys) && $v !== '' && $v !== null, ARRAY_FILTER_USE_BOTH);
        ksort($f);
        $s = $salt;
        foreach ($f as $v) { $s .= '&' . $v; }
        return strtoupper(hash_hmac('sha256', $s, $salt));
    },
];

$endpoint = 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction';

foreach ($tests as $name => $hashFn) {
    $txnRefNo = 'T' . date('ymdHis') . substr(md5($name), 0, 3);
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

    $hash = $hashFn($payload, $salt);
    $payload['pp_SecureHash'] = $hash;

    $resp = Http::withHeaders(['Content-Type' => 'application/json'])->post($endpoint, $payload);
    $code = $resp->json('pp_ResponseCode');
    $msg  = $resp->json('pp_ResponseMessage');
    echo "[$name]\n  Code: $code | Msg: $msg\n\n";
    
    usleep(300000);
}
