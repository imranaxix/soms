<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

$user = App\Models\User::find(2);
echo "Merchant ID: [" . $user->jazzcash_merchant_id . "]\n";
echo "Password:    [" . $user->jazzcash_password . "]\n";
echo "Salt:        [" . $user->jazzcash_integrity_salt . "]\n";

// Also test hash generation manually to compare
$payload = [
    'pp_Version'           => '2.0',
    'pp_TxnType'           => 'MWALLET',
    'pp_Language'          => 'EN',
    'pp_MerchantID'        => $user->jazzcash_merchant_id,
    'pp_Password'          => $user->jazzcash_password,
    'pp_TxnRefNo'          => 'TTEST001',
    'pp_Amount'            => '100000',
    'pp_TxnCurrency'       => 'PKR',
    'pp_TxnDateTime'       => '20260702103000',
    'pp_BillReference'     => 'TTEST001',
    'pp_Description'       => 'Platform facilitated order payment',
    'pp_TxnExpiryDateTime' => '20260703103000',
    'pp_MobileNumber'      => '03123456789',
    'pp_CNIC'              => '345678',
    'pp_ReturnURL'         => 'http://localhost:8000/jazzcash/callback',
];

// Filter empty, sort, build string
$filtered = array_filter($payload, fn($v) => $v !== '' && $v !== null);
ksort($filtered);

$hashString = $user->jazzcash_integrity_salt;
foreach ($filtered as $value) {
    $hashString .= '&' . $value;
}

echo "\nHash string:\n" . $hashString . "\n";
echo "\nComputed hash: " . strtoupper(hash_hmac('sha256', $hashString, $user->jazzcash_integrity_salt)) . "\n";
