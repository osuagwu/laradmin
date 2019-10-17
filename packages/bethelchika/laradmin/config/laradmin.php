<?php

return [

    /**
     * User Management
     */
    'banned_usergroup_id' => env('LARADMIN_BANNED_USERGROUP_ID', 1),
    'admin_usergroup_id' => env('LARADMIN_ADMIN_USERGROUP_ID', 2),
    'cp_id' => env('LARADMIN_CP_ID', 1),
    'super_id' => env('LARADMIN_SUPER_ID', 2),
    'guest_id' => env('LARADMIN_GUEST_ID', 5),

    /**
     * General Laravel validation rule for general fields. Primarily used for user password
     * See Laravel docs for how to define validation  rules. THE RULES HERE HOWEVER SHOULD BE
     * SPECIFIED USING ARRAY.
     * Help message that instruct on the password format should be set in the Laravel lang
     * file called passwords(e.g. /resources/lang/en/passwords.php) using the 'password' key.
     *
     * NOTE: To make sure that the same password rule is used site-wide, make sure that this rule
     * is used in the Laravel's App\Http\Controllers\Auth\RegisterController and
     * App\Http\Controllers\Auth\ResetPasswordController.
     *
     */
    'rules'=>[
        'password' => [
            'required',
            'confirmed',
            'string',
            ['min',6],
            //['regex','/[a-z]/'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Auth Management
    |--------------------------------------------------------------------------
    |
    | Controls if a restricted user should be forced to log out
    |
    */
    'log_out_restricted_user' => env('LARADMIN_LOG_OUT_RESTRICTED_USER', true),

    /**
     * Each login attemp is logged. In order to save space use this setting to define the max 
     * number of login attemps logs you would want each user to have.
     */
    'login_attempt_max_rows'=>8,

    /**
     * The total number of security answers a user should have for valid security questions. Note
     * that that there should be enough questions available for a user to answer in the first 
     * place 
     */
    'security_answers_count'=>2,

    /**
     * The length of time in seconds an auth verification code should be valid for.
     */
    'auth_verification_code_expiry'=>10800,

    /**
     * During auth vericifation every attempt to go to any route will be 
     * redirected back to the verification page. Here you can enter the 
     * list of path that can still work during verification. Note that 
     * any path starting with the given paths will also be allowed to work.
     */
    'auth_verification_except_path'=>['/u/contact-us'],

    /**
     * Plugins path. Empty string sets it to default path
     */
    'plugins_path'=>env('LARADMIN_PLUGINS_PATH',''),

    /**
     * Navigation file
     */
    'navigation_file'=>env('LARADMIN_NAVIGATION_FILE',storage_path('navigation.nav')),

    /**
     * The brand colors
     *
     * @var array
     */
     'brands'=>[
                'primary' => '#4A4A7F',
                'info' => '#7EA3CC',
                'success' => '#00cc66',
                'warning' => '#F4A261',
                'danger' => '#ed6a5a',
                'subtle' => '#f4f4f4',
                'secondary'=>'#D17A22'
     ],

     /**
      * Set the cookie consent params
      */
     'cookie_consent'=>[
        'enable' => env('LARADMIN_COOKIE_CONSENT_ENABLE', true), // Use this setting to enable the cookie consent dialog.
        'name'=>'cookie_consent', //  The name of the cookie in which we store if the user has agreed to accept the conditions.
        'lifetime'=>7300,// Set the cookie duration in days.  Default is 365 * 20.

     ],

     /**
      * Theme. This theme is intended to be used for public pages (wp pages) but not the user 
      * settings. The user settings and the admin pages are not affected by this theme.
      */
      //'theme'=>env('LARADMIN_THEME','default'),

    /**
     * Used to enable or disable Wordpress. Only enable this after installing Wordpress
    */
    'wp_enable'=>env('LARADMIN_WP_ENABLE',false),

    /**
    * The path to wordpress relative to the public path
    */
    'wp_rpath'=>env('LARADMIN_WP_RPATH','/wp'),

    /**
     * Array of menu names/tags that should be imported from WP.
     */
    'wp_menus'=>env('LARADMIN_WP_MENUS',['primary']),

    /**
     * If true comments will require approval before they can be displayed
     */
    'comment_approve'=>false,
    /**
    * The Url path prefix for larus posts. e.g a Larus post is assessed through: http://localhost/{{larus_post_url_prefix}}/post-slug
     */
    'larus_post_url_prefix'=>'larus-post',

    /**
    * The Url path prefix for pages. e.g a page is assessed through: http://localhost/{{page_url_prefix}}/page-slug
     */
    'page_url_prefix'=>env('LARADMIN_PAGE_URL_PREFIX','page'),


    /**
     * Enable authorisation of wordpress pages. It is not very often that  authorisation
     * of public pages is required. So if not required, disable this functionality to
     * reduced the number of database queries performed to carry out authorisation
     */
    'wp_page_auth'=>env('LARADMIN_WP_PAGE_AUTH',false),

    /**
     * Boolean used to allowe site privacy to be read from Wordpress.
     */
    'wp_use_privacy'=>true,

    /**
     * Define social feeds params.
     * 
     * Twitter require setting the followings in the config/services of Laravel app:
     * 'twitter' => [
     *       'twitter_user_id'='316660916',
     *       'consumer_api_key'='...',
     *       'consumer_api_secret'='...',
     *       'access_token'='...',
     *       'access_token_secret'='...'
     *      ]
     * 
     * Instagram requires settings the followings in the config/services of Laravel app.
     */
    'social_feeds'=>[
        'limit'=>4,// Max number of feeds. Set to zero to turn off feeds.
        'providers'=>['twitter'=>true, // Feed providers
                        'facebook'=>false,
                        'instagram'=>false,
                    ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Section Gradient
    |--------------------------------------------------------------------------
     * The regular gradient will flow from color A to B only once. If this config is false
     * then the gradients flow from the colour A to the other colour B may repeat itself.
     */
    'regular_section_gradient'=>env('LARADMIN_REGULAR_SECTION_GRADIENT',true),


    /*
    |--------------------------------------------------------------------------
    | Section PERMISSION KEYS
    |--------------------------------------------------------------------------
    *  NOTE: Enable these configurations if you want to change the following source id.
    */

    //  /**
    //  * Returns the key for accessing permission table for table
    //  *
    //  * @param string $database_connection
    //  * @param string $database
    //  * @param string $table_prefix
    //  * @param string $table_name
    //  * @return string
    //  */
    // 'table_source_id'=>function($database_connection,$database,$table_prefix,$table_name){
    //     return $database_connection.'/'.$database.'/'.$table_prefix.'/'.$table_name; // Note that changing this key will make already applied permission inaccessible, which makes it seem like the previous permissions do not exist.
    // },



    // /**
    //  * Returns the key for accessing permission table for a route
    //  *
    //  * @param Route $route
    //  * @return string
    //  */
    // 'route_source_id'=>function(\Illuminate\Routing\Route $route){
    //     return implode('|', $route->methods()) . ':' . $route->uri . ':' . $route->getActionName();// Note that changing this key will make already applied permission inaccessible, which makes it seem like the previous permissions do not exist
    // },


    // /**
    //  * Returns the key for accessing permission table for a route prefix
    //  *
    //  * @return string
    //  */
    // 'route_prefix_source_id'=>function(\Illuminate\Routing\Route $route){
    //     return $route->getPrefix();// Note that changing this key will make already applied permission inaccessible, which makes it seem like the previous permissions do not exist
    //  },

     /*
    |--------------------------------------------------------------------------
    | GEO IP
    | 
    |--------------------------------------------------------------------------
    */

    'geoip'=>[
        'db_city_filename'=>'/geoip2/GeoLite2-City.mmdb',//The City filename of the geo location ip db relative to laravel root. 
    ],


    /*
    |--------------------------------------------------------------------------
    | Billing
    |--------------------------------------------------------------------------
    */
    'billing'=>[
        'stripe'=>[//Note that most of these stripe config should already exist in config('services.stripe.key') but we are replicating them here so all our billing config can be together.
            'key'=>env('STRIPE_KEY'),
            'secret'=>env('STRIPE_SECRET'),
            'currency'=>env('CASHIER_CURRENCY'),
            'subscription'=>[
                'trial_days'=>1,// Subscription trial period
                'max_quantity'=>10,//The maximum quantity for a stripe subscription
            ],
        ],
        'paypal'=>[
            //TODO
        ]
    ],


];
