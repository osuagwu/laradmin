<?php
namespace BethelChika\Laradmin\Permission\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Route;
use BethelChika\Laradmin\WP\Models\Page;
use BethelChika\Laradmin\Source;

trait Helpers
{    
    /**
     * Get permissions formatted for ui for a given source
     *
     * @param string $source_type
     * @param string $source_name
     * @return void
     */
    public function uiSourcePermissions($source_type,$source_name){
        $sid=$source_type.':'.$source_name;
        $permissions=DB::table('main_permissions')->where('source','=',$sid)->get();
        $temp=[];
        foreach($permissions as $perm){

            if($perm->user_id){
                $perm->data_id=$perm->user_id;
                $user=DB::table('users')->select(['name','email'])->where('id',$perm->user_id)->first();
                $perm->name=$user->name;
                $perm->isGroup=0;
                $perm->email=$user->email;
                
            }elseif($perm->user_group_id){
                $perm->data_id=$perm->user_group_id;
                $user_group=DB::table('user_groups')->select('name')->where('id',$perm->user_group_id)->first();
                $perm->name=$user_group->name;
                $perm->isGroup=1;
                $perm->email='group';
            }else{
                $perm->name='*Unknown*';
                $perm->isGroup='';
            }
            $temp[]=$perm;
        }
        $permissions=$temp;
        return $permissions;
    }


}