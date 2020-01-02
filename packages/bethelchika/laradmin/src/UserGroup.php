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
}
