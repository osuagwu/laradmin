# Configurations
## Social login
The following configurations should be added in the Laravel config/services.php for social login to work.
```php
    'facebook' => [
        //Login
        'client_id' => env('FACEBOOK_CLIENT_ID'),         // Your facebook Client ID
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'), // Your facebook Client Secret
        'redirect' => env('FACEBOOK_CLIENT_CALLBACK_URL','/u/social-user/callback/facebook'),
    ],


    'google' => [
        //Login
        'client_id' => env('GOOGLE_CLIENT_ID'),         // Your google Client ID
        'client_secret' => env('GOOGLE_CLIENT_SECRET'), // Your google Client Secret
        'redirect' => env('GOOGLE_CLIENT_CALLBACK_URL','/u/social-user/callback/google'),
    ],

```
> Note that only Facebook and Google are implemented currently.