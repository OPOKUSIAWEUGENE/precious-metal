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
            'bot_user_default_channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'oauth' => [
            'auth_url' => env('GOOGLE_OAUTH_AUTH_URL', 'https://accounts.google.com/o/oauth2/v2/auth'),
            'token_url' => env('GOOGLE_OAUTH_TOKEN_URL', 'https://oauth2.googleapis.com/token'),
            'userinfo_url' => env('GOOGLE_OAUTH_USERINFO_URL', 'https://openidconnect.googleapis.com/v1/userinfo'),
            'redirect_uri' => env('GOOGLE_OAUTH_REDIRECT_URI'),
            'passenger_redirect_uri' => env('GOOGLE_OAUTH_PASSENGER_REDIRECT_URI'),
            'scope' => env('GOOGLE_OAUTH_SCOPE', 'openid email profile'),
            'response_type' => env('GOOGLE_OAUTH_RESPONSE_TYPE', 'code'),
            'access_type' => env('GOOGLE_OAUTH_ACCESS_TYPE', 'offline'),
            'prompt' => env('GOOGLE_OAUTH_PROMPT', 'consent'),
            'include_granted_scopes' => env('GOOGLE_OAUTH_INCLUDE_GRANTED_SCOPES', 'true'),
        ],
    ],

    'hubtel' => [
        'base_url' => env('HUBTEL_BASE_URL', 'https://api.hubtel.com'),
        'client_id' => env('HUBTEL_CLIENT_ID'),
        'client_secret' => env('HUBTEL_CLIENT_SECRET'),
        'merchant_account' => env('HUBTEL_MERCHANT_ACCOUNT'),
        'callback_url' => env('HUBTEL_CALLBACK_URL'),
        'return_url' => env('HUBTEL_RETURN_URL'),
    ],

    'default_gateway' => env('DEFAULT_PAYMENT_GATEWAY', 'HUBTEL'),

];
