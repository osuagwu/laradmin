<?php

namespace BethelChika\Laradmin;

use Carbon\Carbon;
use BethelChika\Laradmin\UserMessage;
use Illuminate\Support\Facades\Hash;
use BethelChika\Laradmin\Events\UserHardDelete;
use BethelChika\Laradmin\Traits\UserManagement;
use BethelChika\Laradmin\Traits\AuthManagement;


class User extends \App\User
{
    use UserManagement, AuthManagement;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];


    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = [
        'created_at',
        'updated_at',
        'hard_deleted_at',
        'self_delete_initiated_at',
        'self_deactivated_at',
        'current_login_at',
        'last_login_at'
    ];


    //Relationship to UserGroupMap
    function userGroupMap(){
        return $this->hasMany('BethelChika\Laradmin\UserGroupMap');
    }

    //Relationship to COnfirmation
    function confirmations(){
        return $this->hasMany('BethelChika\Laradmin\Confirmation');
    }


     /**
    * Relationship to UserMessage
    */
    function userMessages(){
        return $this->hasMany('BethelChika\Laradmin\UserMessage');
    }

    /**
    * Relationship to UserMessage
    */
    function sentUserMessages(){
        return $this->hasMany('BethelChika\Laradmin\UserMessage','creator_user_id');
    }

    /**
     * Relationshipt to linked social user
     *
     * @return void
     */
    public function socialUsers(){
        return $this->hasMany('BethelChika\Laradmin\Social\Models\socialUser');
    }
    

    /**
     * Hard deletes a user. Hard delete is stronger that soft delete but does not actually wipe the user off completely
     * @param void
     * @return boolean
     */
    function hardDelete(){
        $this->name='Deleted user';
        $this->email=str_random(2).time().'@na.com';
        $this->status=-1;
        $this->is_active=0;
        $this->password=Hash::make((str_random(10)));
        $this->hard_deleted_at=Carbon::now();
        //TODO: set the rest of the profile information to null

        //Fire off event
        event(new UserHardDelete($this));

        //
        $this->save();

        

    }

      /**
     * Initiate self deletion of user account
     *
    * @param  void
    * @return mixed \Carbon\Carbon or false 
    */
    function initiateSelfDelete(){
        $USER_SELF_DELETION_DAYS=14;//TODO: Need to move to settings when implemented
        $now=Carbon::now();
        $this->self_delete_initiated_at=$now;
        if($this->save()){
            return $now->addDays($USER_SELF_DELETION_DAYS);
        }else{
            return false;
        }
    }

    /**
     * Cancel self deletion of user account
     *
    * @param  void
    * @return boolean 
    */
    function cancelSelfDelete(){
        $this->self_delete_initiated_at=null;
        if($this->save()){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Self deactivate account
     *
    * @param  void
    * @return boolean
    */
    function selfDeactivate(){
        $this->self_deactivated_at=Carbon::now();
        if($this->save()){
            return true;
        }else{
            return false;
        }
    }

    /**
    * Self reactivate account for use if auto reactivation fails
    *
    * @see $this->autoReactivate
    */
    function selfReactivate(){
        
       return $this->autoReactivate(0);
    }

    /**
    * Auto reactivate account
    *
    * @param  boolean $flashMsg when true, reactivation messages will be flashed
    * @return int 0:Could not reactivate; 1:successfully reactivated; 2:was not deactivated
    */
    function autoReactivate($flashMsg=true){
        if ($this->self_deactivated_at){
            $this->self_deactivated_at=null;

            if($this->save()){
                if($flashMsg){
                    session()->flash('info','Your account was reactivated.');
                }
                return 1;
            }else{
                return 0;
            }
        }else{//it is not deactivated
            return 2;
        }
        
        
    }


    /**
     * Confirms users email
     *
     * @return boolean true if all goes well but false when cannot save 
     */
    public function confirmEmail(){
        $this->confirmations()
        ->where('user_id','=',$this->id)
        ->where('type','=','email_confirmation')
        ->delete();//delete confirmation 
        $this->status=1;
        return $this->save();
        
    }

    /**
     * Checks is email is confirmed
     *
     * @return boolean True if email is confirmed and false otherwise
     */
    public function isEmailConfirmed(){
        return !($this->status==-1);
    }

    /**
     * Remove the email address attached to this user
     *
     * @return boolean True if all went well but false otherwise
     */
    public function releaseEmail(){
        $this->email=$this->email.'RELEASE.EMAIL';
        return $this->save();
    }



    /**
     * Returns the unread messages of a user
     * @param void
     * @return int
     */
    function unReadUserMessages(){
        return UserMessage::getUnReads($this);
        
    }

    /**
     * Destroy notifications by this user
     * @param void
     * @return boolean
     */
    public function destroyNotifications(){
        $this->notifications()->delete();
        return true;
    }
    /**
     * Limits the number of notifications by deleteing the oldest notifications by this user
     * @param void
     * @return boolean
     */
    public function limitNotifications(){
        $NOTIFICATIONS_LIMIT=4;//TODO: Move and read from settings when createad


        $cp=$this->getSystemUser();
        if(!($cp->is($this))){
            $notifications=$this->notifications;
            if($notifications->count() >= $NOTIFICATIONS_LIMIT){
        
                $notifications->last()->delete();
                
                //Delete those read a while ago
                $readWhileAgos = $notifications->filter(function ($notification, $key) {
                    return !($notification->read_at == null) and $notification->read_at->gte(Carbon::now()->addDays(1));

                });
                $readWhileAgos->each(function($readWhileAgo,$key){
                    $readWhileAgo->delete();
                });
                
            }
        }        
        return true;
    }
    
    /**
     * Set avatar for the user using specified link
     *
     * @param string $link The avatar link
     * @return boolean True on success
     */
    public function setAvatar($link){
         $this->avatar=$link;
         return $this->save();
    }
   
    /**
     * Get all alerts for this user
     *
     * @return array Messages describing alerts
     */
    function getAlerts(){
        $alerts=[];
        if(!$this->isEmailConfirmed()){
            $alerts['ALERT100']='Your email is not confirmed. Please confirm your email. ';
        }

        if(!$this->email){
            $alerts['ALERT101']='You have no email. Please set an email for your account';
        }

        if(!$this->password){
            $alerts['ALERT102']='You have not set a password. Please set a password';
        }

        return $alerts;
    }
}
