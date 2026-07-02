<?php
$salt = '519zxxy265';
$responsePayload = [
    "pp_TxnType" => "MWALLET",
    "pp_Version" => "2.0",
    "pp_Amount" => "1000000",
    "pp_AuthCode" => "",
    "pp_BillReference" => "T260702070932F1",
    "pp_Language" => "EN",
    "pp_MerchantID" => "MC825731",
    "pp_ResponseCode" => "110",
    "pp_ResponseMessage" => "Please provide valid value for pp_SecureHash.",
    "pp_RetreivalReferenceNo" => "",
    "pp_SubMerchantID" => "",
    "pp_TxnCurrency" => "PKR",
    "pp_TxnDateTime" => "20260702070932",
    "pp_TxnRefNo" => "T260702070932F1",
    "pp_MobileNumber" => "03123456789",
    "pp_CNIC" => "345678",
    "pp_DiscountedAmount" => "",
    "ppmpf_1" => "",
    "ppmpf_2" => "",
    "ppmpf_3" => "",
    "ppmpf_4" => "",
    "ppmpf_5" => ""
];

$targetHash = "5BE893D73C36C7EE5D558F0AF4FA78C3F389D387C16C1C1E9CF7A26BCFBA09E7";

$filtered = array_filter($responsePayload, function($v, $k) { 
    return $v !== '' && $v !== null && $k !== 'pp_SecureHash'; 
}, ARRAY_FILTER_USE_BOTH);

ksort($filtered);
$str = $salt;
foreach ($filtered as $v) {
    $str .= '&' . $v;
}
$hash = strtoupper(hash_hmac('sha256', $str, $salt));

echo "Calculated Response Hash: " . $hash . "\n";
echo "Target Response Hash:     " . $targetHash . "\n";
echo "Match: " . ($hash === $targetHash ? "YES" : "NO") . "\n";
