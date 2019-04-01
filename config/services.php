<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),         // Your facebook Client ID
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'), // Your facebook Client Secret
        'redirect' => env('FACEBOOK_CLIENT_CALLBACK_URL','/social-auth-callback/facebook'),
    ],


    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),         // Your google Client ID
        'client_secret' => env('GOOGLE_CLIENT_SECRET'), // Your google Client Secret
        'redirect' => env('GOOGLE_CLIENT_CALLBACK_URL','/social-auth-callback/google'),
    ],

    'twitter' => [
        'client_id' => env('TWITTER_CLIENT_ID'),         // Your twitter Client ID
        'client_secret' => env('TWITTER_CLIENT_SECRET'), // Your twitter Client Secret
        'redirect' => env('TWITTER_CLIENT_CALLBACK_URL','/social-auth-callback/twitter'),
    ],

    'linkedin' => [
        'client_id' => env('LINKEDIN_CLIENT_ID'),         // Your LINKEDIN Client ID
        'client_secret' => env('LINKEDIN_CLIENT_SECRET'), // Your LINKEDIN Client Secret
        'redirect' => env('LINKEDIN_CLIENT_CALLBACK_URL','/social-auth-callback/linkedin'),
    ],

];