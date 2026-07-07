<?php
// Replicate the EXACT hash calculator algorithm from window.js

$salt = '2ss4g2u62u';

// This is the payload from the last transaction (T2607071246236BEU)
// as entered into the hash calculator by the user
$request = [
    'pp_Amount'            => '1000',
    'pp_BillReference'     => 'T2607071246236BEU',
    'pp_Description'       => 'Platform facilitated order payment',
    'pp_Language'          => 'EN',
    'pp_MerchantID'        => 'MC829737',
    'pp_Password'          => 'dtt1z38yuy',
    'pp_ReturnURL'         => 'https://contempt-racism-paralyses.ngrok-free.dev/jazzcash/callback',
    'pp_SubMerchantID'     => '',
    'pp_TxnCurrency'       => 'PKR',
    'pp_TxnDateTime'       => '20260707124623',
    'pp_TxnExpiryDateTime' => '20260707134623',
    'pp_TxnRefNo'          => 'T2607071246236BEU',
    'pp_TxnType'           => '',
    'pp_Version'           => '1.1',
    'ppmpf_1'              => '',
    'ppmpf_2'              => '',
    'ppmpf_3'              => '',
    'ppmpf_4'              => '',
    'ppmpf_5'              => '',
];

// --- Replicate window.js algorithm EXACTLY ---

// 1. Remove pp_SecureHash, sort keys
$sorted = [];
ksort($request);
foreach ($request as $k => $v) {
    if ($k !== 'pp_SecureHash') {
        $sorted[$k] = $v;
    }
}

// 2. Build finalString
$finalString = $salt . '&';
foreach ($sorted as $value) {
    $value = (string) $value;
    $finalString .= $value;
    if ($value !== null && $value !== '') {
        $finalString .= '&';
    }
}
// 3. Strip trailing &
$finalString = substr($finalString, 0, strlen($finalString) - 1);

echo "Final string: " . $finalString . "\n\n";

// 4. HMAC-SHA256 (raw UTF-8, no ISO conversion - matches Node.js crypto)
$hash = strtoupper(hash_hmac('sha256', $finalString, $salt));

echo "Our hash:    " . $hash . "\n";
echo "Calculator:  77452FD30645EF0F3050B125B8EDA4837F8654A6FA2935E039BF934A1A6CED34\n";
echo "Match: " . ($hash === '77452FD30645EF0F3050B125B8EDA4837F8654A6FA2935E039BF934A1A6CED34' ? 'YES ✅' : 'NO ❌') . "\n";
