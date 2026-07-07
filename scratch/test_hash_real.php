<?php
// Test with the actual sandbox credentials to verify hash
// This should match what the JazzCash sandbox hash calculator produces

$salt = '2ss4g2u62u';

// Simulate a payload like we'd send
$txnRefNo = 'T2607071246236BEU';
$payload = [
    'pp_Amount'            => '1000',
    'pp_BillReference'     => $txnRefNo,
    'pp_Description'       => 'Platform facilitated order payment',
    'pp_Language'          => 'EN',
    'pp_MerchantID'        => 'MC829737',
    'pp_Password'          => 'dtt1z38yuy',
    'pp_ReturnURL'         => 'https://contempt-racism-paralyses.ngrok-free.dev/jazzcash/callback',
    'pp_SubMerchantID'     => '',
    'pp_TxnCurrency'       => 'PKR',
    'pp_TxnDateTime'       => '20260707124623',
    'pp_TxnExpiryDateTime' => '20260707134623',
    'pp_TxnRefNo'          => $txnRefNo,
    'pp_TxnType'           => '',
    'pp_Version'           => '1.1',
    'ppmpf_1'              => '',
    'ppmpf_2'              => '',
    'ppmpf_3'              => '',
    'ppmpf_4'              => '',
    'ppmpf_5'              => '',
];

// Filter empty strings (as our code does)
$filtered = array_filter($payload, fn($v) => $v !== null && $v !== '');
ksort($filtered);

$str = $salt . '&' . implode('&', $filtered);
$iso = mb_convert_encoding($str, 'ISO-8859-1', 'UTF-8');
$hash = strtoupper(hash_hmac('sha256', $iso, $salt));

echo "Hash string: " . $str . "\n\n";
echo "Our hash:    " . $hash . "\n";
echo "Calculator:  77452FD30645EF0F3050B125B8EDA4837F8654A6FA2935E039BF934A1A6CED34\n";
echo "Match: " . ($hash === '77452FD30645EF0F3050B125B8EDA4837F8654A6FA2935E039BF934A1A6CED34' ? 'YES ✅' : 'NO ❌') . "\n";

echo "\n--- Keys included in hash ---\n";
echo implode(', ', array_keys($filtered)) . "\n";
