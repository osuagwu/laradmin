# Installation
## Install Laravel
It is better to try with a fresh Laravel install first. So install Laravel.
### Auth
Make Authentication.
```
php artisan make:auth
```
Then go to *.../app/Http/Controllers/Auth/RegisterController.php* and change the import statement,

```php
use App\User;
```

to
```php
`use BethelChika\Laradmin\User;`.
```

## Install Laradmin files
Currently this project is not on Packagist; the best way to use this project is to copy the project folder into your Laravel project.

To do this:
1) If it does not exists already create a a folder named `project` in the root of your Laravel project
2) Copy Laradmin github into the folder you have just created. 
3) Yoo should now have a folder path starting with `bethelchika/laradmin` inside `packages` folder.


Now to configure the project, add psr4 in Laravel's composer.json under 'autoload'
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
    BethelChika\Laradmin\Providers\LaradminServiceProvider::class
];
```

## Dependencies
You can install the dependencies using composer thus
```
composer require intervention/image
composer require doctrine/dbal
composer require laravel/socialite
composer require rezozero/mixedfeed
composer require jenssegers/agent
composer require geoip2/geoip2
```
 

### Wordpress
If you plan to  use Wordpress the you should also require:
```
composer require jgrossi/corcel
```


### Notification
You should create the notification table and migrate it thus.
```
php artisan notifications:table
php artisan migrate
```



## Publishing
To publish config and assets use the following commands:
php artisan vendor:publish --tag=laradmin-config
php artisan vendor:publish --tag=laradmin-asset

If you also want to publish the views use:
php artisan vendor:publish --tag=laradmin-view

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


php artisan migrate

php artisan db:seed --class="BethelChika\Laradmin\Seeds\LaradminDatabaseSeeder"

> If you do not seed the users table you may get 404 errors from the pre-authorise middleware as it tries to load a guest user which may be empty.

> Warnings: Configure laravel mail, else you might get mail errors/warnings during seeding etc. These errors however will not affect the installation process.

> Tip: The command `php artisan migrate:refresh`  leads to error because some fields of `users` table has been set to nullable contrary to Laravel, to allow social registration. You should use `php artisan migrate:fresh` instead

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
Laradmin config include `rules.password` which allows you to set password rules what can conviniently be used sitewide. The rules set in this config will automatically be used by laradmin when setting/resetting a password. To make sure that the same password rule is used sitewide, make sure that this rule is used in the Laravel's App\Http\Controllers\Auth\RegisterController and App\Http\Controllers\Auth\ResetPasswordController. For the former you can just modify the `validation()` method and for the latter you should override the  `rules()` method provided through a trait.

## Modifications of Controllers
ONLY IF YOU WANT TO MAKE CHANGES TO A LARADMIN CONTROLLER, you can make copies of the Controllers from Laradmin and place them in Laravel app's default controller folder.
To do this make a folder named *Laradmin* in your controller folder; Then create further folder *User* and *CP* inside 'Laradmin'.
Now you can E.g copy  'BethelChika\Laradmin\Http\Controllers\User\UserProfileController.php' into your '...app\Http\Controllers\Laradmin\User\' to modify it.

>Don't forget to change namespace after moving the controller. In the example above you will need to change the namespace from `BethelChika\Laradmin\Http\Controllers\User` to `App\Http\Controllers\Laradmin\User`

>However you could simply override a route of interest by redeclearing the route yourself, then provide your own controller which could be a modified copy of the original from Laradmin
