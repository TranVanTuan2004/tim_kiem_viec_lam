<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'zalopay' => [
        'app_id' => env('ZALOPAY_APP_ID', '123015'), // App ID từ documentation
        'key1' => env('ZALOPAY_KEY1', 'ZALOPAY_KEY1'),
        'key2' => env('ZALOPAY_KEY2', 'ZALOPAY_KEY2'),
        'endpoint' => env('ZALOPAY_ENDPOINT', 'https://sb-openapi.zalopay.vn/v2/create'),
        'callback_url' => env('ZALOPAY_CALLBACK_URL', 'http://localhost:8000/admin/subscriptions/payment/callback'),
        'redirect_url' => env('ZALOPAY_REDIRECT_URL', 'http://localhost:8000/admin/subscriptions/payment/return'),
        'environment' => env('ZALOPAY_ENVIRONMENT', 'sandbox'), // sandbox hoặc production
    ],

    'vnpay' => [
        'tmn_code' => env('VNPAY_TMN_CODE', 'TMN_CODE'),
        'hash_secret' => env('VNPAY_HASH_SECRET', 'HASH_SECRET'),
        'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
        'return_url' => env('VNPAY_RETURN_URL', 'http://localhost:8000/admin/subscriptions/vnpay/return'),
        'callback_url' => env('VNPAY_CALLBACK_URL', 'http://localhost:8000/admin/subscriptions/vnpay/callback'),
        'environment' => env('VNPAY_ENVIRONMENT', 'sandbox'), // sandbox hoặc production
        'version' => '2.1.0',
        'command' => 'pay',
        'curr_code' => 'VND',
        'locale' => 'vn',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
    ],
];
