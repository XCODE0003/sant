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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],
    'tinkoff' => [
        'terminal_key' => env('TINKOFF_TERMINAL_KEY'),
        'password' => env('TINKOFF_PASSWORD'),
        'test_mode' => env('TINKOFF_TEST_MODE', false),
    ],
    'resend' => [
        'key' => env('RESEND_KEY'),
    ],
    'telegram' => [
        'token' => env('TELEGRAM_BOT_TOKEN', ''),
        'admins' => array_filter(explode(',', env('TELEGRAM_ADMIN_CHATS', ''))),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
