<?php

namespace BethelChika\Laradmin;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    protected $guarded = ['id','created_at','updated_at'];
    
    //Relationship to UserGroupMap
    function userGroupMap(){
        return $this->hasMany('BethelChika\Laradmin\UserGroupMap');
    }
}
