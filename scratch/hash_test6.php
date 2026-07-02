<?php
$payload = [
    'pp_Version'            => '2.0',
    'pp_Password'           => 'a02z77h1x1',
    'pp_Amount'             => '1000000',
    'pp_Description'        => 'Platform facilitated order payment',
];

$filtered1 = array_filter($payload, function ($value, $key) {
    return $key !== 'pp_SecureHash'
        && $key !== 'pp_Password'
        && $value !== ''
        && $value !== null;
}, ARRAY_FILTER_USE_BOTH);

$filtered2 = $payload;
unset($filtered2['pp_Password']);
$filtered2 = array_filter($filtered2, fn($v) => $v !== '' && $v !== null);

echo "Filtered 1 (ARRAY_FILTER_USE_BOTH):\n";
print_r($filtered1);

echo "\nFiltered 2 (unset):\n";
print_r($filtered2);

