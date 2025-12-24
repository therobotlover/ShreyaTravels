<?php

return [
    'mode' => env('BKASH_MODE', 'sandbox'),
    'app_key' => env('BKASH_APP_KEY'),
    'app_secret' => env('BKASH_APP_SECRET'),
    'username' => env('BKASH_USERNAME'),
    'password' => env('BKASH_PASSWORD'),
    'base_url' => env('BKASH_BASE_URL'),
    'callback_url' => env('BKASH_CALLBACK_URL'),
    'enabled' => (bool) (env('BKASH_APP_KEY') && env('BKASH_APP_SECRET') && env('BKASH_USERNAME') && env('BKASH_PASSWORD') && env('BKASH_BASE_URL') && env('BKASH_CALLBACK_URL')),
];

