<?php

namespace BethelChika\Laradmin;

use Illuminate\Database\Eloquent\Model;

class UserGroupMap extends Model
{
    protected $guarded = ['id','created_at','updated_at'];

    //Relationship to user
    function user(){
        return $this->belongsTo('BethelChika\Laradmin\User');
    }

    //Relationship to UserGroup
    function userGroup(){
        return $this->belongsTo('BethelChika\Laradmin\UserGroup');
    }
}
