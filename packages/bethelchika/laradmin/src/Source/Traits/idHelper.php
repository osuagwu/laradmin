<?php
namespace BethelChika\Laradmin\Source\Traits;

use Illuminate\Routing\Route;
use BethelChika\Laradmin\Source;
use Illuminate\Database\Eloquent\Model;

trait idHelper
{
    /**
     * This trait defines important keys for accessing the permission table for 
     * different sources. Note that changing these keys will make already applied 
     * permission inaccessible, which makes it seem like the previous permissions 
     * do not exist. So keys should be changed before any permission is applied.
     
     */





    /**
     * Returns the key for accessing permission table for table
     * Note that this key does not support multiple databases. 
     * So if you have two tables with the same name but in 
     * different databases, they will be seen as the same 
     * in the permission table. Use getTableKeyMultiple(...) to avoid this issue.
     *
     * @param string $database_connection
     * @param string $database
     * @param string $table_prefix
     * @param string $table_name
     * @return string
     */
    public static function getTableSourceId($database_connection,$database,$table_prefix,$table_name)
    {
        $config=config('laradmin.table_source_id');
        
        if($config){
            return $config($database_connection,$database,$table_prefix,$table_name);
        }
        return $database_connection.'/'.$database.'/'.$table_prefix.'/'.$table_name; // Note that changing this key will make already applied permission inaccessible, which makes it seem like the previous permissions do not exist.

    }

   
    /**
     * Returns the key for accessing permission table for a route
     *
     * @param \Illuminate\Routing\Route $route
     * @return string
     */
    public static function getRouteSourceId(Route $route)
    {
        $config=config('laradmin.route_source_id');
        if($config){
            return $config($route);
        }
        return implode('|', $route->methods()) . ':' . $route->uri . ':' . $route->getActionName(); // Note that changing this key will make already applied permission inaccessible, which makes it seem like the previous permissions do not exist.
    }

 /**
     * Returns the key for accessing permission table for a route prefix
     *  @param \Illuminate\Routing\Route $route $route The prefix including a preceding forward slash
     * @return string
     */
    public static function getRoutePrefixSourceId($route)
    {
        $config=config('laradmin.route_prefix_source_id');
        if($config){
            return $config($route);
        }
        return $route->getPrefix() ; // Note that changing this key will make already applied permission inaccessible, which makes it seem like the previous permissions do not exist.
    }


    /**
     * Given a model instance, this method returns the string that uniquely 
     * identifies the underlying table. The returned 
     * string can be used to access the permission table.
     * 
     *
     * @param Model $model
     * @return string
     */
    public static function getTableSourceIdFromModel(Model $model){
        $config=$model->getConnection()->getConfig();
        $table_type_key=Source::getTableSourceId($config['name'],$config['database'],$config['prefix'],$model->getTable());
        //$table_name=$model->getTable();
        return $table_type_key;//.':'.$table_name;
    }
   
}
