<?php
return [
    'sandbox'     => env('JAZZCASH_SANDBOX', true),
    'endpoint_ma' => env('JAZZCASH_SANDBOX', true)
        ? 'https://sandbox.jazzcash.com.pk/ApplicationAPI/API/Payment/DoTransaction' 
        : 'https://payments.jazzcash.com.pk/ApplicationAPI/API/Payment/DoTransaction',
    'return_url'  => env('JAZZCASH_RETURN_URL'),
];
