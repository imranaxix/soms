<?php
$salt = '519zxxy265';
$responsePayload = [
    "pp_TxnType" => "MWALLET",
    "pp_Version" => "2.0",
    "pp_Amount" => "1000000",
    "pp_AuthCode" => "",
    "pp_BillReference" => "T260702063950TST",
    "pp_Language" => "EN",
    "pp_MerchantID" => "MC825731",
    "pp_ResponseCode" => "110",
    "pp_ResponseMessage" => "Please provide valid value for pp_SecureHash.",
    "pp_RetreivalReferenceNo" => "",
    "pp_SubMerchantID" => "",
    "pp_TxnCurrency" => "PKR",
    "pp_TxnDateTime" => "20260702063950",
    "pp_TxnRefNo" => "T260702063950TST",
    "pp_MobileNumber" => "03123456789",
    "pp_CNIC" => "345678",
    "pp_DiscountedAmount" => "",
    "ppmpf_1" => "",
    "ppmpf_2" => "",
    "ppmpf_3" => "",
    "ppmpf_4" => "",
    "ppmpf_5" => ""
];

$targetHash = "EA96E1EE705B15A499AF5AFA7C77849B3CC847EC58D79D81B6C99D330C869B1C";

function tryHash(array $payload, string $salt, bool $removeEmpty) {
    if ($removeEmpty) {
        $payload = array_filter($payload, function($v) { return $v !== '' && $v !== null; });
    }
    ksort($payload);
    $str = $salt;
    foreach ($payload as $v) {
        $str .= '&' . $v;
    }
    return strtoupper(hash_hmac('sha256', $str, $salt));
}

echo "Target Hash: " . $targetHash . "\n";
echo "Remove Empty: " . tryHash($responsePayload, $salt, true) . "\n";
echo "Keep Empty:   " . tryHash($responsePayload, $salt, false) . "\n";

// What if empty fields are not removed but they don't append anything? No.
// What if it uses sha256 instead of hmac?
function tryHashSha256(array $payload, string $salt, bool $removeEmpty) {
    if ($removeEmpty) {
        $payload = array_filter($payload, function($v) { return $v !== '' && $v !== null; });
    }
    ksort($payload);
    $str = $salt;
    foreach ($payload as $v) {
        $str .= '&' . $v;
    }
    return strtoupper(hash('sha256', $str));
}
echo "SHA256 Keep Empty: " . tryHashSha256($responsePayload, $salt, false) . "\n";
echo "SHA256 Remove Empty: " . tryHashSha256($responsePayload, $salt, true) . "\n";
