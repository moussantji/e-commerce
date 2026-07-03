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

    'fedapay' => [
        'base_url' => env('FEDAPAY_BASE', 'https://sandbox.fedapay.com/api'),
        'public_key' => env('FEDAPAY_PUBLIC_KEY'),
        'secret_key' => env('FEDAPAY_SECRET_KEY'),
    ],
    
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),

        // Audiences (client IDs) acceptées pour la vérification des idToken
        // envoyés par l'app mobile (@react-native-google-signin). On accepte
        // plusieurs valeurs : web / android / ios. Renseignez-les dans .env,
        // séparées par des virgules, ex :
        //   GOOGLE_ALLOWED_CLIENT_IDS=xxx-web.apps..., yyy-android.apps...
        'allowed_client_ids' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('GOOGLE_ALLOWED_CLIENT_IDS', ''))
        ))),
    ],

];
