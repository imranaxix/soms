<?php
/**
 * Let's try every single possible subset of the transaction fields.
 * We'll use the EXACT same fields and values the library uses but try 
 * all combos of which fields to include in the hash.
 * 
 * Key fields that COULD be in the hash:
 * - pp_Amount, pp_BillReference, pp_CNIC, pp_Description, pp_MobileNumber
 * - pp_TxnCurrency, pp_TxnDateTime, pp_TxnExpiryDateTime
 * - pp_MerchantID, pp_Password, pp_Version, pp_TxnType, pp_Language, pp_TxnRefNo
 * - pp_ReturnURL
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$mfg = App\Models\User::find(2);
$salt      = $mfg->jazzcash_integrity_salt;
$merchantId = $mfg->jazzcash_merchant_id;
$password   = $mfg->jazzcash_password;

$endpoint = 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction';

// These are the "auth" fields that get added after hash (don't participate in hash)
$authFields = ['pp_Version', 'pp_TxnType', 'pp_Language', 'pp_MerchantID', 'pp_Password', 'pp_TxnRefNo'];

// Generate a fresh transaction for each test variation
$i = 0;
$tests = [
    // Vary which "auth" fields are included in hash 
    'All except ReturnURL'                => fn($p) => $p, 
    'Only Amount+CNIC+Mobile+Bill+Desc+Cur+Time+Expiry' => fn($p) => array_intersect_key($p, array_flip(['pp_Amount','pp_CNIC','pp_MobileNumber','pp_BillReference','pp_Description','pp_TxnCurrency','pp_TxnDateTime','pp_TxnExpiryDateTime'])),
    'Add MerchantID+Password to above' => fn($p) => array_intersect_key($p, array_flip(['pp_Amount','pp_CNIC','pp_MobileNumber','pp_BillReference','pp_Description','pp_TxnCurrency','pp_TxnDateTime','pp_TxnExpiryDateTime','pp_MerchantID','pp_Password'])),
    'Add Version+Type+Lang+Ref' => fn($p) => array_intersect_key($p, array_flip(['pp_Amount','pp_CNIC','pp_MobileNumber','pp_BillReference','pp_Description','pp_TxnCurrency','pp_TxnDateTime','pp_TxnExpiryDateTime','pp_MerchantID','pp_Password','pp_Version','pp_TxnType','pp_Language','pp_TxnRefNo'])),
];

foreach ($tests as $label => $filterFn) {
    $i++;
    $txnRefNo = 'T' . date('ymdHis') . 'C' . $i;
    $dateTime  = date('YmdHis');
    $expiry    = date('YmdHis', strtotime('+1 hour'));
    
    $allFields = [
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
    ];
    
    $hashInput = $filterFn($allFields);
    $hashInput = array_filter($hashInput, fn($v) => $v !== '' && $v !== null);
    ksort($hashInput);
    $s = $salt;
    foreach ($hashInput as $v) { $s .= '&' . $v; }
    $hash = strtoupper(hash_hmac('sha256', $s, $salt));
    
    $payload = array_merge($allFields, ['pp_SecureHash' => $hash]);
    
    $resp = Http::withHeaders(['Content-Type' => 'application/json'])->post($endpoint, $payload);
    $body = $resp->json();
    $code = $body['pp_ResponseCode'] ?? 'N/A';
    $msg  = $body['pp_ResponseMessage'] ?? 'N/A';
    echo "[$label]\n  Code: $code | Msg: $msg\n\n";
    
    if ($code !== '110') {
        echo ">>> DIFFERENT RESULT! Pre-image was: $s\n\n";
    }
    
    usleep(300000);
}
