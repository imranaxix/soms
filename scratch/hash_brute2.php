<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$mfg = App\Models\User::find(2);
$salt      = $mfg->jazzcash_integrity_salt;
$merchantId = $mfg->jazzcash_merchant_id;
$password   = $mfg->jazzcash_password;

$dateTime  = date('YmdHis');
$expiry    = date('YmdHis', strtotime('+1 day'));

$basePayload = [
    'pp_Version'            => '2.0',
    'pp_TxnType'            => 'MWALLET',
    'pp_Language'           => 'EN',
    'pp_MerchantID'         => (string) $merchantId,
    'pp_Password'           => (string) $password,
    'pp_Amount'             => '1000000',
    'pp_TxnCurrency'        => 'PKR',
    'pp_TxnDateTime'        => $dateTime,
    'pp_Description'        => 'Platform facilitated order payment',
    'pp_TxnExpiryDateTime'  => $expiry,
    'pp_MobileNumber'       => '03123456789',
    'pp_CNIC'               => '345678',
    'pp_ReturnURL'          => 'https://contempt-racism-paralyses.ngrok-free.dev/jazzcash/callback',
];

$strategies = [
    'Standard (Exclude Empty)' => function($p) {
        return array_filter($p, fn($v, $k) => $v !== '' && $v !== null && $k !== 'pp_SecureHash', ARRAY_FILTER_USE_BOTH);
    },
    'Keep Empty Strings' => function($p) {
        return array_filter($p, fn($v, $k) => $v !== null && $k !== 'pp_SecureHash', ARRAY_FILTER_USE_BOTH);
    },
    'Exclude Password' => function($p) {
        return array_filter($p, fn($v, $k) => $v !== '' && $v !== null && $k !== 'pp_SecureHash' && $k !== 'pp_Password', ARRAY_FILTER_USE_BOTH);
    },
    'Exclude ReturnURL' => function($p) {
        return array_filter($p, fn($v, $k) => $v !== '' && $v !== null && $k !== 'pp_SecureHash' && $k !== 'pp_ReturnURL', ARRAY_FILTER_USE_BOTH);
    }
];

$endpoint = 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/2.0/Purchase/DoMWalletTransaction';

$i = 0;
foreach ($strategies as $name => $strategy) {
    $i++;
    // Generate UNIQUE TxnRefNo for each request to avoid duplicate error masking the hash result!
    $txnRefNo = 'T' . date('ymdHis') . str_pad($i, 3, '0', STR_PAD_LEFT);
    
    $payload = $basePayload;
    $payload['pp_TxnRefNo'] = $txnRefNo;
    $payload['pp_BillReference'] = $txnRefNo;

    // Optional empty fields that were in the original payload
    $payload['pp_SubMerchantID'] = '';
    $payload['pp_BankID'] = '';
    $payload['pp_ProductID'] = '';
    $payload['pp_DiscountedAmount'] = '';
    $payload['ppmpf_1'] = '';
    $payload['ppmpf_2'] = '';
    $payload['ppmpf_3'] = '';
    $payload['ppmpf_4'] = '';
    $payload['ppmpf_5'] = '';

    $filtered = $strategy($payload);
    ksort($filtered);
    $hashString = $salt;
    foreach ($filtered as $value) {
        $hashString .= '&' . $value;
    }
    
    $payload['pp_SecureHash'] = strtoupper(hash_hmac('sha256', $hashString, $salt));
    
    $resp = Http::withHeaders(['Content-Type' => 'application/json'])->post($endpoint, $payload);
    echo "[$name] Code: " . $resp->json('pp_ResponseCode') . " | Msg: " . $resp->json('pp_ResponseMessage') . "\n";
    if ($resp->json('pp_ResponseCode') === '000' || $resp->json('pp_ResponseMessage') !== 'Please provide valid value for pp_SecureHash.') {
        echo ">>> FOUND MATCH: $name\n";
    }
    
    // Sleep slightly to ensure different timestamps if needed, though uniquely padding is fine
    usleep(500000);
}
