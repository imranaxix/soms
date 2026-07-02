<?php
$f = ['pp_Version'=>'2.0','pp_TxnType'=>'MWALLET','pp_Language'=>'EN','pp_MerchantID'=>'MC825731','pp_TxnRefNo'=>'T2607020548384LRQ','pp_Amount'=>'1000000','pp_TxnCurrency'=>'PKR','pp_TxnDateTime'=>'20260702054838','pp_BillReference'=>'T2607020548384LRQ','pp_Description'=>'Platform facilitated order payment','pp_TxnExpiryDateTime'=>'20260703054838','pp_MobileNumber'=>'03001234567','pp_CNIC'=>'345678','pp_ReturnURL'=>'http://localhost:8000/jazzcash/callback'];
ksort($f);
echo '519zxxy265&' . implode('&', $f) . "\n";
