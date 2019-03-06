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
 
    /*
    |--------------------------------------------------------------------------
    | AuthManagement 
    |--------------------------------------------------------------------------
    |
    | COntrols if a restricted user should be forced to log out
    |
    */
    'log_out_restricted_user' => env('LARADMIN_LOG_OUT_RESTRICTED_USER', true),
    

    // /**
    //  * CSS Classes
    //  */
    // 'css_classes' => env('LARADMIN_CSS_CLASSES',[
    //     'body_default'=>'',
    //     'body_hero'=>'header-transparent',
    // ] ),

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
                'primary' => '#135B7A',
                'info' => '#7f8fa6',
                'success' => '#108C42',
                'warning' => '#C27116',
                'danger' => '#C23716',
                'subtle' => '#f4f4f4',
     ],
    

    /**
    * The path to wordpress relative to the public path
    */
    'wp_rpath'=>env('LARADMIN_WP_RPATH','/wp'),

    /**
    * The Url path prefix for pages. e.g a page is assessed through: http://localhost/{{page_url_prefix}}/page-slug
     */
    'page_url_prefix'=>env('LARADMIN_PAGE_URL_PREFIX','page'),
];
