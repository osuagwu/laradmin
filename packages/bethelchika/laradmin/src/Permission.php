<?php

namespace BethelChika\Laradmin;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    /*TODO: We need a maintenace functionality that will traverse all the rows in the table and delete entries that could not be found from there source.
    */ 
    protected $guarded = ['id','created_at','updated_at'];

    /**
     * Delete the rows specified by source type and id
     *
     * @param string $source_type
     * @param string $source_id
     * @return boolean
     */
    public static function unlinkSource($source_type,$source_id){
        $rows=Permission::where('source_type',$source_type)
        ->where('source_id',$source_id)->get();
        if($rows){
            foreach($rows as $row){
                $row->delete();
            }
        }
        return true;
    }
}
