<?php

namespace BethelChika\Laradmin;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    protected $guarded = ['id','created_at','updated_at'];
    
    //Relationship to UserGroupMap TODO: This may be a huge change but we need to delete this method and use User::userGroups()
    function userGroupMap(){
        return $this->hasMany('BethelChika\Laradmin\UserGroupMap');
    }

    //Relationship to User
    function users(){
        return $this->belongsToMany('BethelChika\Laradmin\User','users')->withTimestamps();
    }

    /**
     * Add a user to a given group
     * 
     * TODO: this method is untested
     * 
     * @param string $group_name
     * @return boolean|null Returns null if group does not exists or user is already in group
     */
    public static function addUserTo(User $user,$group_name){
        $group=self::select(['id'])->where('name',$group_name)->first();
        if($group){
            if(!$user->isMemberOf($group_name)){
                $map=new UserGroupMap;
                $map->user_id=$user->id;
                $map->user_group_id=$group->id;
                return $map->save();
            }
        }
        return null;
    }
    
    /**
     * Removes a user from a given group
     * 
     * TODO: this method is untested
     *
     * @param User $user
     * @param string $group_name
     * @return boolean|null Returns null if group does not exists or user is not in group
     */
    public static function removeUserFrom(User $user,$group_name){
        $group=self::select(['id'])->where('name',$group_name)->first();
        if ($group) {
            $map=UserGroupMap::where('user_id', $user->id)->where('user_group_id', $group->id)->first();
            if($map){
                return $map->delete();
            }
            
        }
        return null;
    }
}
