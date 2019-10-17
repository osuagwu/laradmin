<?php

namespace BethelChika\Laradmin;

use Ramsey\Uuid\Uuid;
use BethelChika\Laradmin\User;
use Illuminate\Database\Eloquent\Model;
use BethelChika\Laradmin\Traits\UserMessageActions;
use Carbon\Carbon;

//use Illuminate\Notifications\Notifiable;
//use Illuminate\Foundation\Auth\User as Authenticatable;

class UserMessage extends Model
{
    use UserMessageActions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message', 'secret',
    ];
    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = [
       'created_at',
       'updated_at',
       'read_at',
       'deleted_by_sender_at',
       'deleted_by_receiver_at',
       
    ];

    /**
     * The "type" of the auto-incrementing ID..
     *
     * @var string
     */
    protected $keyType = 'uuid';

     /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'channels' => 'array',
    ];

    /**
     * The max allowed size of UserMessage quota for each user.
     *
     * @var int
     */
    public static $quotaMax =20000;


    /**
     * The max allowed size of UserMessage quota for admin.
     *
     * @var int
     */
    public static $adminQuotaMax=20000*1000;
    

    /**
     * Set the secret.
     *
     * @param  string  $value
     * @return void
     */
    public function setSecretAttribute($value)
    {
        $this->attributes['secret'] = bcrypt($value);
    } 
   

    /**
    * Relationship to User
    */
    function user(){
        return $this->belongsTo('BethelChika\Laradmin\User');
    }

    /**
    * Relationship to User
    */
    function sender(){
        return $this->belongsTo('BethelChika\Laradmin\User','creator_user_id');
    }

     /**
    * Relationship to User. The administrator who used CP to create this message
    */
    function adminSender(){
        return $this->belongsTo('BethelChika\Laradmin\User','admin_creator_user_id');
    }

    /**
     * Check if the supplied size is within quota set for admin
     * 
     * @param User $user
    * @return boolean
    */
    public function isWithinAdminQuota(User $user){
        
        return ($this->getQuotaSize() + $user->uer_message_quota)<=UserMessage::$adminQuotaMax;

        
    }

    /**
     * Check if the supplied size is within quota
     * 
     * @param User $user
    * @return boolean
    */
    public function isWithinUserQuota(User $user){
        
        return ($this->getQuotaSize() + $user->uer_message_quota)<=UserMessage::$quotaMax;

        
    }

    /**
     * Get message size
     * 
     * @param void
    * @return int
    */
    public function getQuotaSize(){
        return strlen($this->message); 
    }



    /**
     * Return unread messages for a specified user
     * 
     * @param User $user
    * @return \Illuminate\Support\Collection
    */
    public static function getUnReads(User $user){

        return $user->userMessages()->where(['read_at'=>null,'deleted_by_receiver_at'=>null])->get();
    }
    
    /**
     * Delete the thisuserMessage
     * 
     * @param User $deleter The user who wants to delete, who is either the sender or receiver
    * @return boolean
    */
    public function deleteByUser(User $deleter){
        $this->minusFromQuota($deleter);

        //If it is a self conversation, then delete straight away
        if($this->user_id==$this->creator_user_id){
            return $this->delete();
        }

        /*  The logged in user is the person trying to delete, check if 
        *   she is the sender or user(receiver) and mark delete accordingly
        */
        if($deleter->id==$this->user_id){
            $this->deleted_by_receiver_at=Carbon::now(); 
            
        }else{
            $this->deleted_by_sender_at=Carbon::now();
        
        }
        $this->save();

        //Delete if both sender and reciever has maked messaage as deleted
        if($this->deleted_by_user_at and $this->deleted_by_sender_at){
            return $this->delete();
        }
        return true;

        
    }

    /**
     * Increase the quota of the specifies user by the size of this data or if specified, by the given amount
     * 
     * @param User $user
     * @param int $amount
    * @return boolean
    */
    public function addToQuota(User $user,$amount=null){
            if($amount)$user->user_message_quota+=$amount;
            else $user->user_message_quota+=$this->getQuotaSize();
            return $user->save();
    }

    /**
     * Decrease the quota of the specifies user by the size of this data or if specified, by the given amount
     * 
     * @param User $user
     * @param int $amount
    * @return boolean
    */
    public function minusFromQuota(User $user,$amount=null){
            if($amount) $user->user_message_quota-=$amount;
            else $user->user_message_quota-=$this->getQuotaSize();
            return $user->save();
    }
}
