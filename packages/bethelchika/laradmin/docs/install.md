# Installation
## Install Laravel
It is better to try with a fresh Laravel install first. So install Laravel. Please configure your mail driver as well. You could simply set the env variable to log:
```
MAIL_DRIVER=log
```
### Auth
Make Authentication.
```
composer require laravel/ui --dev
php artisan ui vue --auth
```
Then go to *.../app/Http/Controllers/Auth/RegisterController.php* and change the import statement,

```php
use App\User;
```

to
```php
use BethelChika\Laradmin\User;
```

## Install Laradmin files
Currently this project is not on Packagist; the best way to use this project is to copy the project folder into your Laravel project.

To do this:
1) If it does not exists already create a a folder named `project` in the root of your Laravel project
2) Copy Laradmin github into the folder you have just created. 
3) Yoo should now have a folder path starting with `packages/bethelchika/laradmin` folder.


Now to configure the project, add the psr4 in Laravel's composer.json under 'autoload'
```json
{...
"psr-4": {
            "App\\": "app/",
            "BethelChika\\Laradmin\\": "packages/bethelchika/laradmin/src/"
        }
        ...
}
```

The next is to add the service provider
Edit your Laravel //config/app.php to add the Laradmin service provider thus:

```php
'providers' => [
    ...
    BethelChika\Laradmin\LaradminServiceProvider::class
];
```

## Dependencies
You can install the dependencies using composer thus
```
composer require laravel/helpers
composer require intervention/image
composer require doctrine/dbal
composer require laravel/socialite
composer require rezozero/mixedfeed
composer require jenssegers/agent
composer require geoip2/geoip2
```

If you want to format money with Laradmin and Laravel Cashier is not installed, just install the money package yourself:
```
composer require moneyphp/money
``` 
 

### Wordpress
If you plan to  use Wordpress then you should also require:
```
composer require jgrossi/corcel
```


### Notification
You should create the notification table
```
php artisan notifications:table
```



## Publishing
To publish config and assets use the following commands:

```
php artisan vendor:publish --tag=laradmin-config
```
```
php artisan vendor:publish --tag=laradmin-asset
```

If you also want to publish the views use:
```
php artisan vendor:publish --tag=laradmin-view
```

## Migration and seeding
If you are using MYSQL and havent done so, you should change your Laravel database config so that it uses  `InnoDB` engine. Open config/database and look for a connection named database and make thus:
```php 
'connections' => [
    ...

    'mysql' => [
        'driver' => 'mysql',
        ...
        'engine' => 'InnoDB',
```

```
php artisan migrate
```
```
php artisan db:seed --class="BethelChika\Laradmin\Seeds\LaradminDatabaseSeeder"
```

> If you do not seed the users table you may get 404 errors from the pre-authorise middleware as it tries to load a guest user which may be empty.

> Note: If the `users` is not empty when you run the Laradmin seeder, the seeder will print out variables you need to set. Make sure you read and follow the guide. This is not such a bad thing as it can help make you own Laradmin installation a bit unique b/c your ids of important users may be different from that of other installations. To avoid setting the variables, run the seeder on a fresh `users`table.

> Warnings: Configure Laravel mail, else you might get mail errors/warnings during seeding etc. These errors however will not affect the installation process.

> Note: To allow social registration where a user may not have email or password, Laradmin makes the `email` and `password` columns of the users table *nullable*. This may not be reversed if there are users and with null values in any of these columns, when removing Laradmin.  

## Users model provider
Edit your Laravel's config/auth.php and replace user model in the provider as `BethelChika\Laradmin\User::class`
Thus:
```php
...
'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => BethelChika\Laradmin\User::class,
        ],
    ],
...
```

## Done
If all went well you should be presented with a login page when you try to visit: /u/profile/

Username: admin@localhost

Password: admin

To login as a user using OAuth providers, you should first configure Laravel Socialite using its documentation on https://laravel.com/docs/. The configurations are made in config/services.php of your laravel app. In the file you should set your `redirect` keys to  */u/social-user/callback/facebook* for Facebook, */u/social-user/callback/google* for Google, */u/social-user/callback/twitter* for Twitter, etc. Here are examples: 
```php
....
    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),         // Your facebook Client ID
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'), // Your facebook Client Secret
        'redirect' => env('FACEBOOK_CLIENT_CALLBACK_URL','/u/social-user/callback/facebook'),
    ],


    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),         // Your google Client ID
        'client_secret' => env('GOOGLE_CLIENT_SECRET'), // Your google Client Secret
        'redirect' => env('GOOGLE_CLIENT_CALLBACK_URL','/u/social-user/callback/facebook'),
    ],
....
```

Below is a html for your login and register templates to enable login with facebook and google for example.
```html
<a class="btn btn-primary" href="{{route('social-user-callout','facebook')}}"><i class="fab fa-facebook-f"></i> Login with Facebook </a>

<a class="btn btn-primary" href="{{route('social-user-callout','google')}}"> <i class="fab fa-google"></i> Login with Google </a>

```
### Notes
#### User Login/registration
User login and registration are handled normally by Laravel controllers and views, unless when using social login which is handled by Laradmin.  

#### Password policy
Laradmin config include `rules.password` which allows you to set password rules that can conveniently be used site-wide. The rules set in this config will automatically be used by laradmin when setting/resetting a password. To make sure that the same password rule is used site-wide, make sure that this rule is used in the Laravel's App\Http\Controllers\Auth\RegisterController and App\Http\Controllers\Auth\ResetPasswordController. For the former you can just modify the `validation()` method and for the latter you should override the  `rules()` method provided through a trait. The validation messages should be set in your Laravel validation.php lang file using the 'custom' key as described in Laravel docs. 

## Modifications of Controllers
You could override a route of interest by redeclaring the route yourself in your main application, then provide your own controller which could be a modified copy of the original from Laradmin. You can of course modify or replace the views.
