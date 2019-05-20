<?php
namespace BethelChika\Laradmin\Source\Traits;

use BethelChika\Laradmin\Source;
use Illuminate\Database\Eloquent\Model;

trait AccessString
{   /**
     * Given a model instance, this method returns the string for that uniquely 
     * identifies the underlying table for the specified model. The returned 
     * string can be used to access the permission table.
     * 
     *
     * @param Model $model
     * @return string
     */
    public static function getTableAccessString(Model $model){
        $config=$model->getConnection()->getConfig();
        $table_type_key=Source::getTableTypeKey($config['name'],$config['database'],$config['prefix']);
        $table_name=$model->getTable();
        return $table_type_key.':'.$table_name;
    }
}