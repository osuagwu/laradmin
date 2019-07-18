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
                'primary' => '#4A4A7F',
                'info' => '#7EA3CC',
                'success' => '#00cc66',
                'warning' => '#F4A261',
                'danger' => '#ed6a5a',
                'subtle' => '#f4f4f4',
                'secondary'=>'#D17A22'
     ],
    
    /**
     * Use to enable or disable Wordpress
    */
    'wp_enable'=>env('LARADMIN_WP_ENABLE',true),
     
    /**
    * The path to wordpress relative to the public path
    */
    'wp_rpath'=>env('LARADMIN_WP_RPATH','/wp'),

    /**
     * Array of menu names/tags that should be imported from WP.
     */
    'wp_menus'=>env('LARADMIN_WP_MENUS',['primary']),

    /**
    * The wordpress theme name.
    */
    'wp_theme'=>env('LARADMIN_WP_THEME','twentyseventeen'),


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
    
    
    // /**
    //  * PERMISSION KEYS [Enable these iy you want to change the following source id]
    //  */

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


];
