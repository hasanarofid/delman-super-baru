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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'wablas' => [
        'endpoint' => env('WABLAS_ENDPOINT', 'https://jogja.wablas.com/api/send-message'),
        'token' => env('WABLAS_TOKEN'),
        'secret' => env('WABLAS_SECRET'),
        'delay' => env('WABLAS_DELAY_SECONDS', 10),
        'anti_spam_suffix' => env('WABLAS_ANTI_SPAM_SUFFIX', true),
    ],

];

