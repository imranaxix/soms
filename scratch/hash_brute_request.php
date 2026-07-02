<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$mfg = App\Models\User::find(2);
$salt      = $mfg->jazzcash_integrity_salt;
$merchantId = $mfg->jazzcash_merchant_id;
$password   = $mfg->jazzcash_password;

$endpoint = 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction';

$optionalFields = [
    'pp_Password',
    'pp_ReturnURL',
    'pp_Description',
    'pp_TxnExpiryDateTime',
    'pp_CNIC',
    'pp_BillReference'
];

$numCombinations = 1 << count($optionalFields);
echo "Testing $numCombinations combinations...\n";

// We will send requests in batches of 16 to avoid overwhelming the sandbox, or just run them all with a small sleep.
for ($mask = 0; $mask < $numCombinations; $mask++) {
    $txnRefNo = 'T' . date('ymdHis') . str_pad($mask, 3, '0', STR_PAD_LEFT);
    $dateTime  = date('YmdHis');
    $expiry    = date('YmdHis', strtotime('+1 hour'));

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

    // Determine which fields to EXCLUDE from the hash calculation
    $excluded = [];
    $filtered = $payload;
    foreach ($optionalFields as $i => $field) {
        if ($mask & (1 << $i)) {
            unset($filtered[$field]);
            $excluded[] = $field;
        }
    }

    // Always filter out empty strings and nulls, and pp_SecureHash itself
    $filtered = array_filter($filtered, function ($value, $key) {
        return $key !== 'pp_SecureHash' && $value !== '' && $value !== null;
    }, ARRAY_FILTER_USE_BOTH);

    ksort($filtered);
    $hashString = $salt;
    foreach ($filtered as $value) {
        $hashString .= '&' . $value;
    }
    $hash = strtoupper(hash_hmac('sha256', $hashString, $salt));
    
    $payload['pp_SecureHash'] = $hash;

    // Send the request
    try {
        $resp = Http::timeout(5)->withHeaders(['Content-Type' => 'application/json'])->post($endpoint, $payload);
        $code = $resp->json('pp_ResponseCode');
        $msg = $resp->json('pp_ResponseMessage');
        
        $excludedStr = count($excluded) > 0 ? implode(', ', $excluded) : 'None';
        echo "Mask $mask (Excluded: $excludedStr) -> Code: $code | Msg: $msg\n";
        
        if ($code !== '110' || strpos($msg, 'pp_SecureHash') === false) {
            echo ">>> SUCCESS OR DIFFERENT ERROR FOUND AT MASK $mask! <<<\n";
            echo "Excluded fields: " . implode(', ', $excluded) . "\n";
            echo "Pre-image: $hashString\n";
            break;
        }
    } catch (\Exception $e) {
        echo "Mask $mask failed: " . $e->getMessage() . "\n";
    }
    
    usleep(150000); // 150ms sleep between requests
}
echo "Done brute-forcing requests.\n";
