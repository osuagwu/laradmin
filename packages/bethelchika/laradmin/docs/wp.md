# Wordpress
Laradmin uses Wordpress for document publishing and menu graphical creation.

## Installation
Laradmin currently require that Wordpress is installed in /public folder of Laravel your app. You should follow the usual worpress installation process.  
## Configuration
After installation of Wordpress, you should configure a connection for the Wordpress database in your Laravel database config file. For example add the the following connection:
```php
....
'corcel' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE_WP', 'wordpress'),
            'username' => env('DB_USERNAME_WP', 'root'),
            'password' => env('DB_PASSWORD_WP', 'root'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => 'wp_',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ],
....
```
The connection name should be 'corcel' as shown above.

in your environmental file tell Laradmin that you are ready with Wordpress by setting the following variables:
LARADMIN_WP_ENABLE=true
LARADMIN_WP_RPATH=/wp
LARADMIN_PAGE_URL_PREFIX=page

The first variable tells Laradmin to begin using Wordpress, the seconds defines the folder relative to the Laravel app's public folder, where Worpress is installed. And the third variable tells Laradmin the prefix to use when viewing pages from Wordpress.

## Plugin
TODO: Provide an easy way to copy the pluging and template into the apprioprate Wordpress folders.
Install Laradmin Wordpress plugin in Wordpress and copy page templates to the Worpress template folders. 


## Finish
Now go and start creating pages in Wordpress and create a menu in Wordpress named 'primary'. Add wordpress pages to this  menu and the should show up on your Laravel application.

See Laradmin webpage for documentation on how to using templlates, template metas and page parts to modify pages.



