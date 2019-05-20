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
    * The path to wordpress relative to the public path
    */
    'wp_rpath'=>env('LARADMIN_WP_RPATH','/wp'),

    /**
    * The Url path prefix for pages. e.g a page is assessed through: http://localhost/{{page_url_prefix}}/page-slug
     */
    'page_url_prefix'=>env('LARADMIN_PAGE_URL_PREFIX','page'),

    /**
     * PERMISSION KEYS
     */

     /**
     * Returns the key for accessing permission table for table
     * 
     * @param string $database_connection
     * @param string $database
     * @param string $table_prefix
     * @param string $table_name
     * @return string
     */
    'permission_table_type_key'=>function($database_connection,$database,$table_prefix){
           return $database_connection.'/'.$database.'/'.$table_prefix.'/table'; // Note that changing this key will make already applied permission inaccessible, which makes it seem like the prvious permissions do not exist.
        },

        
    /**
    * Returns the key for accessing permission table for 
    * table when there is more than one databases the 
    * tables are coming from
    *
    * @param string $table_name
    * @param string $database_id An identifier for the database the table
    * @return string
    */
    // 'permission_table_key_multiple'=> function($table_name,$database_id){
    //        return $database_id.':table:'.$table_name;
    //     },


    /**
     * Returns the key for accessing permission table for a route
     *
     * @param Route $route
     * @return string
     */
    'permission_route_type_key'=>function(\Illuminate\Routing\Route $route){
           return 'route:'.implode('|',$route->methods()).':'.$route->uri.':'.$route->getActionName();
        },


     /**
     * Returns the key for accessing permission table for page
     *
     * @return string
     */
    'permission_page_type_key' =>function( ){
            return \BethelChika\Laradmin\WP\Models\Page::class .'/page';
        },


    
     /** 
     * Returns the key for accessing permission table for an item that exists in the sourec table
     *
     * @return string
     */
    'permission_type_key'=>function(){
            return \BethelChika\Laradmin\Source::class;
        }

    /**
     * The User profile theme name
     */
    //'user_profile_theme'=>env('LARADMIN_USER_PROFILE_THEME','default'),

    /**
     * The fromt end theme name
     */
    //'user_page_theme'=>env('LARADMIN_PAGE_THEME','default'),
];
