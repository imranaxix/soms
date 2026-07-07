<?php
// Test hash against JazzCash v4.2 docs example
// Expected: c7689cda7474eb1adcd343fd0c0b676bad0ba66361cc46db589bdb0da4c1c867
// Salt: 0F5DD14AE2
// Fields: pp_Amount=2995, pp_MerchantID=MER123, pp_OrderInfo=A48cvE28

$salt = '0F5DD14AE2';
$data = [
    'pp_Amount'     => '2995',
    'pp_MerchantID' => 'MER123',
    'pp_OrderInfo'  => 'A48cvE28',
];

ksort($data);
$str = $salt . '&' . implode('&', $data);
$iso = mb_convert_encoding($str, 'ISO-8859-1', 'UTF-8');
$hash = hash_hmac('sha256', $iso, $salt);

echo "Input string: " . $str . "\n";
echo "Hash:         " . $hash . "\n";
echo "Expected:     c7689cda7474eb1adcd343fd0c0b676bad0ba66361cc46db589bdb0da4c1c867\n";
echo "Match: " . ($hash === 'c7689cda7474eb1adcd343fd0c0b676bad0ba66361cc46db589bdb0da4c1c867' ? 'YES' : 'NO') . "\n";
