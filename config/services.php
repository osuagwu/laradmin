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
        'page_url'=>env('FACEBOOK_PAGE_URL','https://www.facebook.com/Webferendum-104239010935473/'),// Page url
        //Login
        'client_id' => env('FACEBOOK_CLIENT_ID'),         // Your facebook Client/App ID
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'), // Your facebook Client Secret
        'redirect' => env('FACEBOOK_CLIENT_CALLBACK_URL','/u/social-user/callback/facebook'),
    ],


    'google' => [
        //Login
        'client_id' => env('GOOGLE_CLIENT_ID'),         // Your google Client ID
        'client_secret' => env('GOOGLE_CLIENT_SECRET'), // Your google Client Secret
        'redirect' => env('GOOGLE_CLIENT_CALLBACK_URL','/u/social-user/callback/google'),
    ],

    'twitter' => [
        'handle'=>env('TWITTER_HANDLE','laradmin'),
        //Login
        'client_id' => env('TWITTER_CLIENT_ID'),         // Your twitter Client ID
        'client_secret' => env('TWITTER_CLIENT_SECRET'), // Your twitter Client Secret
        'redirect' => env('TWITTER_CLIENT_CALLBACK_URL','/u/social-user/callback/twitter'),
        
        //Feeds
        'user_id'=>env('TWITTER_USER_ID'), // Note this determines the feed source

        // App credentials
        'consumer_api_key'=>env('TWITTER_CONSUMER_API_KEY'),
        'consumer_api_secret'=>env('TWITTER_CONSUMER_API_SECRET'),
        'access_token'=>env('TWITTER_ACCESS_TOKEN'),
        'access_token_secret'=>env('TWITTER_ACCESS_TOKEN_SECRET'),
    ],

    'linkedin' => [
        //Login
        'client_id' => env('LINKEDIN_CLIENT_ID'),         // Your LINKEDIN Client ID
        'client_secret' => env('LINKEDIN_CLIENT_SECRET'), // Your LINKEDIN Client Secret
        'redirect' => env('LINKEDIN_CLIENT_CALLBACK_URL','/u/social-user/callback/linkedin'),
    ],

];
