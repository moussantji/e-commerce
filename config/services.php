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

    /*
    |--------------------------------------------------------------------------
    | SMS (vérification du numéro de téléphone)
    |--------------------------------------------------------------------------
    |
    | driver=log : le code OTP est écrit dans les logs (développement).
    | En production, brancher la passerelle (Orange/Moov API) dans
    | App\Support\PhoneVerification@sendViaGateway.
    |
    */

    'sms' => [
        'driver' => env('SMS_DRIVER', 'log'),
        // Passerelle Orange (Mali) : identifiants https://developer.orange.com
        'orange' => [
            'client_id' => env('SMS_ORANGE_CLIENT_ID'),
            'client_secret' => env('SMS_ORANGE_CLIENT_SECRET'),
            'sender' => env('SMS_ORANGE_SENDER'), // ex : tel:+22370000000 (numéro Orange alloué)
            'base_url' => env('SMS_ORANGE_BASE_URL', 'https://api.orange.com'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp (vérification des numéros par code OTP)
    |--------------------------------------------------------------------------
    |
    | otp_driver=log  : code écrit dans les logs (dev/test, gratuit).
    | otp_driver=gateway : passerelle HTTP sur TON numéro (Ultramsg, Green-API...).
    | otp_driver=meta : Meta WhatsApp Cloud API (production). Accès API
    |   gratuit ; chaque OTP livré est facturé par Meta au tarif template
    |   du pays destinataire (template utilitaire avec {{1}} = le code).
    |
    */

    'whatsapp' => [
        'otp_driver' => env('WHATSAPP_OTP_DRIVER', 'log'),
        // Passerelle HTTP sur TON numéro WhatsApp (recommandé : le site génère
        // le code, la passerelle l'envoie depuis ton numéro connecté en QR).
        // - Ultramsg : URL=https://api.ultramsg.com/{instance}/messages/chat
        //   METHOD=POST FORMAT=form TO=to TEXT=body TOKEN={token} (+ SUCCESS_KEY=sent)
        // - Green-API : URL=https://api.green-api.com/waInstance{id}/sendMessage/{token}
        //   METHOD=POST FORMAT=json TO=chatId TEXT=message SUFFIX=@c.us (token dans l'URL)
        'gateway' => [
            'url' => env('WHATSAPP_GATEWAY_URL'),
            'method' => env('WHATSAPP_GATEWAY_METHOD', 'POST'),
            'format' => env('WHATSAPP_GATEWAY_FORMAT', 'json'), // json|form
            'to_param' => env('WHATSAPP_GATEWAY_TO', 'to'),
            'text_param' => env('WHATSAPP_GATEWAY_TEXT', 'body'),
            'to_suffix' => env('WHATSAPP_GATEWAY_SUFFIX', ''),
            'token' => env('WHATSAPP_GATEWAY_TOKEN'),
            'token_param' => env('WHATSAPP_GATEWAY_TOKEN_PARAM', 'token'),
            'success_key' => env('WHATSAPP_GATEWAY_SUCCESS_KEY', ''),
        ],
        // Meta Cloud API : https://developers.facebook.com (app + n° WhatsApp Business)
        'token' => env('WHATSAPP_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        // Template UTILITY à créer/approuver dans le dashboard Meta, ex FR :
        // « Boutique : votre code de vérification est {{1}}. Il expire dans 10 minutes. »
        'template' => env('WHATSAPP_OTP_TEMPLATE', 'otp_boutique'),
        'lang' => env('WHATSAPP_OTP_LANG', 'fr'),
        'base_url' => env('WHATSAPP_BASE_URL', 'https://graph.facebook.com/v21.0'),
    ],

];
