<?php
return [
    'sandbox'     => env('JAZZCASH_SANDBOX', true),
    // HTTP POST Page Redirect endpoint for v1.1 (not API v2.0)
    'endpoint_ma' => env('JAZZCASH_SANDBOX', true)
        ? 'https://sandbox.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform/'
        : 'https://payments.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform/',
    'return_url'  => env('JAZZCASH_RETURN_URL'),
];