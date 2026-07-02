<?php
/**
 * DEFINITIVE TEST - Let's work backwards from JazzCash's own hash.
 * We have:
 * - Our payload (known)
 * - JazzCash's response hash: 433E60BB0A1AFF79EC718E46577D6663DB4321C9510A6469FFBD0F58CA6A6D55
 * 
 * In the response, JazzCash returns our submitted data as-is, plus their computed hash.
 * JazzCash's hash is what THEY computed. If this is the REQUEST hash they expected,
 * we can find the pre-image by hashing different field combinations.
 * 
 * Response payload (what JazzCash echoed back):
 * pp_TxnType: MWALLET, pp_Version: 2.0, pp_Amount: 100, pp_AuthCode: '',
 * pp_BillReference: BILLREF001, pp_Language: EN, pp_MerchantID: MC825731,
 * pp_ResponseCode: 110, pp_ResponseMessage: ..., pp_TxnCurrency: PKR,
 * pp_TxnDateTime: 20260702064856, pp_TxnRefNo: T260702064856M1,
 * pp_MobileNumber: 03123456789, pp_CNIC: 345678
 * 
 * JazzCash hash: 433E60BB...
 * 
 * Let's find what produces 433E60BB:
 * - Try hashing ONLY the response fields (no Description, no ExpiryDateTime, no ReturnURL)
 */

$salt = '519zxxy265';
$target = '433E60BB0A1AFF79EC718E46577D6663DB4321C9510A6469FFBD0F58CA6A6D55';

// The request payload
$requestPayload = [
    'pp_Version'            => '2.0',
    'pp_TxnType'            => 'MWALLET',
    'pp_Language'           => 'EN',
    'pp_MerchantID'         => 'MC825731',
    'pp_Password'           => 'a02z77h1x1',
    'pp_TxnRefNo'           => 'T260702064856M1',
    'pp_Amount'             => '100',
    'pp_TxnCurrency'        => 'PKR',
    'pp_TxnDateTime'        => '20260702064856',
    'pp_BillReference'      => 'BILLREF001',
    'pp_Description'        => 'Test',
    'pp_TxnExpiryDateTime'  => '20260703064856',
    'pp_MobileNumber'       => '03123456789',
    'pp_CNIC'               => '345678',
    'pp_ReturnURL'          => 'https://contempt-racism-paralyses.ngrok-free.dev/jazzcash/callback',
];

function testHash(array $p, string $salt, string $target, string $label): bool {
    $p = array_filter($p, fn($v, $k) => $v !== '' && $v !== null && $k !== 'pp_SecureHash', ARRAY_FILTER_USE_BOTH);
    ksort($p);
    $s = $salt;
    foreach ($p as $v) { $s .= '&' . $v; }
    $hash = strtoupper(hash_hmac('sha256', $s, $salt));
    $match = ($hash === $target);
    if ($match) {
        echo "✓ MATCH: $label\n";
        echo "  Pre-image: $s\n";
    }
    return $match;
}

echo "Target: $target\n\n";

// Test: request minus Password
$t = $requestPayload; unset($t['pp_Password']);
testHash($t, $salt, $target, 'Minus Password');

// Test: request minus Password, minus ReturnURL
$t = $requestPayload; unset($t['pp_Password']); unset($t['pp_ReturnURL']);
testHash($t, $salt, $target, 'Minus Password, Minus ReturnURL');

// Test: request minus Description
$t = $requestPayload; unset($t['pp_Description']);
testHash($t, $salt, $target, 'Minus Description');

// Test: request minus Description and Password
$t = $requestPayload; unset($t['pp_Description']); unset($t['pp_Password']);
testHash($t, $salt, $target, 'Minus Description & Password');

// Test: request minus ExpiryDateTime
$t = $requestPayload; unset($t['pp_TxnExpiryDateTime']);
testHash($t, $salt, $target, 'Minus ExpiryDateTime');

// Test: request minus ExpiryDateTime and Password
$t = $requestPayload; unset($t['pp_TxnExpiryDateTime']); unset($t['pp_Password']);
testHash($t, $salt, $target, 'Minus ExpiryDateTime & Password');

// Test: request minus ReturnURL and ExpiryDateTime and Description  
$t = $requestPayload; unset($t['pp_ReturnURL']); unset($t['pp_TxnExpiryDateTime']); unset($t['pp_Description']);
testHash($t, $salt, $target, 'Minus ReturnURL, ExpiryDateTime, Description');

// Test all combos of removing certain fields
$optionalFields = ['pp_Password', 'pp_ReturnURL', 'pp_Description', 'pp_TxnExpiryDateTime', 'pp_BillReference', 'pp_CNIC'];
for ($mask = 0; $mask < (1 << count($optionalFields)); $mask++) {
    $t = $requestPayload;
    $removed = [];
    foreach ($optionalFields as $i => $f) {
        if ($mask & (1 << $i)) {
            unset($t[$f]);
            $removed[] = $f;
        }
    }
    if (testHash($t, $salt, $target, 'Removed: ' . implode(', ', $removed ?: ['none']))) {
        break;
    }
}

echo "Done searching.\n";
