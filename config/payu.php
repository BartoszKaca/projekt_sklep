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
    | For school projects, you can set 'simulate' to bypass PayU entirely
    | and simulate successful payments.
    |
    */
    'environment' => env('PAYU_ENV', env('PAYU_ENVIRONMENT', 'sandbox')),
    
    /*
    |--------------------------------------------------------------------------
    | Simulate Payments (optional - for demos only)
    |--------------------------------------------------------------------------
    |
    | When set to true, payments will be simulated and automatically approved
    | without actually connecting to PayU. 
    | 
    | Default: false (uses real PayU sandbox)
    |
    */
    'simulate' => env('PAYU_SIMULATE', false),

    /*
    |--------------------------------------------------------------------------
    | PayU Credentials
    |--------------------------------------------------------------------------
    |
    | These are your PayU merchant credentials. You can obtain these from
    | your PayU merchant panel.
    |
    | Supports both naming conventions:
    | - PAYU_MERCHANT_POS_ID or PAYU_POS_ID
    | - PAYU_OAUTH_CLIENT_ID or PAYU_CLIENT_ID
    | - PAYU_OAUTH_CLIENT_SECRET or PAYU_CLIENT_SECRET
    */
    'pos_id' => env('PAYU_MERCHANT_POS_ID', env('PAYU_POS_ID')),
    'signature_key' => env('PAYU_SIGNATURE_KEY'),
    'client_id' => env('PAYU_OAUTH_CLIENT_ID', env('PAYU_CLIENT_ID')),
    'client_secret' => env('PAYU_OAUTH_CLIENT_SECRET', env('PAYU_CLIENT_SECRET')),
];
