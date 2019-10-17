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
        $this->$name=$value;
        $r= $this->save();
       
        return $r;
    }

    /**
     * Checks if the email associated to this social user is confirmed
     *
     * @return boolean
     */
    public function isEmailConfirmed(){
        return !$this->status==-1;
    }

    /**
     * Getter for the associated email
     *
     * @return string
     */
    public function getEmail(){
        return $this->social_email;
    }
}
