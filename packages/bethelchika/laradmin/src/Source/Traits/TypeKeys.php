<?php
namespace BethelChika\Laradmin\Source\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Route;
use BethelChika\Laradmin\WP\Models\Page;
use BethelChika\Laradmin\Source;

trait TypeKeys
{
    /**
     * This trait defines important keys for accessing the permission table for different sources. Note that changing this key will make already applied permission inaccessible, which makes it seem like the prvious permissions do not exist.
     
     */





    /**
     * Returns the key for accessing permission table for table
     * Note that this key does not suport multiple databases. 
     * So if you have two tables with the same name but in 
     * different databases, they will be seen as the same 
     * in the permission table. Use getTableKeyMultiple(...) to avoide this issue.
     *
     * @param string $database_connection
     * @param string $database
     * @param string $table_prefix
     * @param string $table_name
     * @return string
     */
    public static function getTableTypeKey($database_connection,$database,$table_prefix)
    {
        $config=config('laradmin.permission_table_type_key');
        
        if($config){
            return $config($database_connection,$database,$table_prefix);
        }
        return $database_connection.'/'.$database.'/'.$table_prefix.'/table'; // Note that changing this key will make already applied permission inaccessible, which makes it seem like the prvious permissions do not exist.

    }

   
    /**
     * Returns the key for accessing permission table for a route
     *
     * @param Route $route
     * @return string
     */
    public static function getRouteTypeKey(Route $route)
    {
        $config=config('laradmin.permission_route_type_key');
        if($config){
            return $config($route);
        }
        return 'route:' . implode('|', $route->methods()) . ':' . $route->uri . ':' . $route->getActionName(); // Note that changing this key will make already applied permission inaccessible, which makes it seem like the prvious permissions do not exist.
    }

 /**
     * Returns the key for accessing permission table for a route
     *
     * @param Route $route
     * @return string
     */
    public static function getRoutePrefixTypeKey(Route $route)
    {
        $config=config('laradmin.permission_route_type_key');
        if($config){
            return $config($route);
        }
        return 'route_prefix:' . implode('|', $route->methods()) . ':' . $route->uri . ':' . $route->getActionName(); // Note that changing this key will make already applied permission inaccessible, which makes it seem like the prvious permissions do not exist.
    }


    /**
     * Returns the key for accessing permission table for page
     *
     * @param Page $page
     * @return string
     */
    public static  function getPageTypeKey()
    {
        $config=config('laradmin.permission_page_type_key');
        if($config){
            return $config();
        }
        return Page::class .'/page'; // Note that changing this key will make already applied permission inaccessible, which makes it seem like the prvious permissions do not exist.
    }

    /** 
     * Returns the key for accessing permission table for an item that exists in the sourec table
     *
     * @return void
     */
    public static function getTypeKey()
    {
        $config=config('laradmin.permission_type_key');
        if($config){
            return $config();
        }
        return Source::class; // Note that changing this key will make already applied permission inaccessible, which makes it seem like the prvious permissions do not exist.

    }
}
