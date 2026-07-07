<?php
// Test BOTH algorithms against the official JazzCash docs example
// Salt: 0F5DD14AE2 | Fields: pp_Amount=2995, pp_MerchantID=MER123, pp_OrderInfo=A48cvE28
// Expected: c7689cda7474eb1adcd343fd0c0b676bad0ba66361cc46db589bdb0da4c1c867

$salt = '0F5DD14AE2';
$data = [
    'pp_Amount'     => '2995',
    'pp_MerchantID' => 'MER123',
    'pp_OrderInfo'  => 'A48cvE28',
];
ksort($data);

// --- Algorithm A: Filter empty, join with &, prepend salt ---
$filteredA = array_filter($data, fn($v) => $v !== null && $v !== '');
ksort($filteredA);
$strA = $salt . '&' . implode('&', $filteredA);
$hashA = hash_hmac('sha256', $strA, $salt);

// --- Algorithm B: Calculator style (include empty, & only after non-empty) ---
$strB = $salt . '&';
foreach ($data as $v) {
    $v = (string)$v;
    $strB .= $v;
    if ($v !== '') $strB .= '&';
}
$strB = rtrim($strB, '&');
$hashB = hash_hmac('sha256', $strB, $salt);

$expected = 'c7689cda7474eb1adcd343fd0c0b676bad0ba66361cc46db589bdb0da4c1c867';

echo "String A: $strA\n";
echo "String B: $strB\n";
echo "Strings same: " . ($strA === $strB ? 'YES' : 'NO') . "\n\n";
echo "Hash A:   $hashA\n";
echo "Hash B:   $hashB\n";
echo "Expected: $expected\n";
echo "A Match: " . ($hashA === $expected ? 'YES ✅' : 'NO ❌') . "\n";
echo "B Match: " . ($hashB === $expected ? 'YES ✅' : 'NO ❌') . "\n";
