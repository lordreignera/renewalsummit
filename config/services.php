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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'swapp' => [
        'base_url'     => env('SWAPP_BASE_URL', 'https://swapp.co.ug/api/mm'),
        'client_id'    => env('SWAPP_CLIENT_ID'),
        'api_key'      => env('SWAPP_API_KEY'),
        'api_secret'   => env('SWAPP_API_SECRET'),
        'callback_url' => env('SWAPP_CALLBACK_URL'),
        'return_url'   => env('SWAPP_RETURN_URL'),
    ],

    'africaistalking' => [
        'username'  => env('AFRICAISTALKING_USERNAME'),
        'api_key'   => env('AFRICAISTALKING_API_KEY'),
        'sender_id' => env('AFRICAISTALKING_SENDER_ID', ''),
    ],

];
