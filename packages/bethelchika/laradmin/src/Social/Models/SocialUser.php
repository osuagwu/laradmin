<?php

namespace BethelChika\Laradmin\Social\Models;

use Illuminate\Database\Eloquent\Model;

class SocialUser extends Model
{
    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = [
        'created_at',
        'updated_at'
    ];
    // Relationship to User
    public function user()
    {
        return $this->belongsTo('BethelChika\Laradmin\User');
    }

    /**
     * Update an arbitrary field in this model
     *
     * @param string $name Field name
     * @param string $value Field value
     * @return boolean True on success
     */
    public function updateField($name,$value){
        //$d=$this->updated_at->diffForHumans().'<br>';//TODO:delete
        //dd(strcmp($value,$this->social_token));
        $this->$name=$value;
        $r= $this->save();
       
        //dd($this->updated_at->diffForHumans());
        return $r;
    }
}
