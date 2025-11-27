<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PayU Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the PayU environment. Set to 'sandbox' for testing
    | or 'secure' for production.
    |
    */
    'environment' => env('PAYU_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | PayU Credentials
    |--------------------------------------------------------------------------
    |
    | These are your PayU merchant credentials. You can obtain these from
    | your PayU merchant panel.
    |
    */
    'pos_id' => env('PAYU_POS_ID'),
    'signature_key' => env('PAYU_SIGNATURE_KEY'),
    'client_id' => env('PAYU_CLIENT_ID'),
    'client_secret' => env('PAYU_CLIENT_SECRET'),
];
