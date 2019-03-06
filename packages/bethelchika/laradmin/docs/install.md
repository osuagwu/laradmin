# Installation
## Install Laradmin files(TODO)
Copy the bethelchika/laradmin project into packages in the root of laravel.

Add psr4 in Laravel's composer.json under 'autoload'
```json
{...
"psr-4": {
            "App\\": "app/",
            "BethelChika\\Laradmin\\": "packages/bethelchika/laradmin/src/"
        }
        ...
}
```

## Service provider
Edit //config/app.php
```php
'providers' => [
    ...
    BethelChika\Laradmin\Providers\LaradminServiceProvider::class
];
```



## Migrations and vendor publishing,
php artisan vendor:publish
php artisan migrate
php artisan db:seed --class="BethelChika\Laradmin\Seeds\LaradminDatabaseSeeder"

## Users model provider
Edit config/auth.php and specify user model in the provider as BethelChika\Laradmin\User::class

# Post Install
## Modifications of Controllers
You can make copies of the Controllers from Laradmin and place them in your app's controller folder.
To do this make a folder named 'Laradmin' in your controller folder; Then create further folder 'User' and 'CP' inside 'Laradmin'.
Now you can E.g copy  'BethelChika\Laradmin\User\UserProfileController.php' into your '...app\Http\Controllers\Laradmin\User\' to modify it.