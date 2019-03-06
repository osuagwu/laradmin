<?php

namespace BethelChika\Laradmin;

use Illuminate\Database\Eloquent\Model;

class Confirmation extends Model
{
    protected $guarded = ['id','created_at','updated_at'];

    //Relationship to user
    function user(){
        return $this->belongsTo('BethelChika\Laradmin\User');
    }

}
