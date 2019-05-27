<?php

namespace BethelChika\Laradmin\Policies;

use BethelChika\Laradmin\User;
use BethelChika\Laradmin\UserMessage;
//use BethelChika\Laradmin\Permission\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;
use BethelChika\Laradmin\Source;
use BethelChika\Laradmin\Laradmin;
use Illuminate\Database\Eloquent\Model;

class UserMessagePolicy
{
    use HandlesAuthorization;
    /**
    * @var \BethelChika\Laradmin\Permission\Permission
    * Permission object
    *
    */
    public $perm;

    /**
     * The key for accessing permission, comprising source type key and the table name
     *
     * @var string
     */
    public $tableSourceId;

    /**
    * Create a new policy instance.
   * @param Laradmin $laradmin
    * @return void
    */
    public function __construct(Laradmin $laradmin){
        $this->perm=$laradmin->permission;

        //Get table info
        $temp_user_msg=new UserMessage();
        $this->tableSourceId=Source::getTableSourceIdFromModel($temp_user_msg);
        unset($temp_user_msg);
    }
    public function before(User $user){
        
        if($user->id==$user->getSuperId())return true;
        
    }

     /**
     * Checks that the message is sent to or sent by the specified user
     *
     * @param BethelChika\Laradmin\User $user
     * @param UserMessage $userMessage
     * @return boolean
     */
    private function isMy(User $user,UserMessage $userMessage){
        

        //Check if this object is created by this user
        try { //Try to access a property that might not exist
            $myId=$userMessage->creator_user_id;

            if ($user->id==$myId){
                return true;
            }
        }catch(Exception $ex){

        }

        try { //Try to access a property that might not exist
            $myId=$userMessage->user_id;

            if ($user->id==$myId){
                return true;
            }
        }catch(Exception $ex){

        }

        
        return false;
    }

    /**
     * Determine whether the user can view the UserMessage in control panel
     *
     * @param  \BethelChika\Laradmin\User  $user
     * @param  \BethelChika\Laradmin\UserMessage $userMessage
     * @return mixed
     */
    public function cpView(User $user, UserMessage $userMessage)
    {
        //Check at the Table level
        if(!$this->perm->can($user,'table',$this->tableSourceId,'read',$userMessage)){
            return false;
        }

        //Check at the model level
        if(!$this->modelCheckHelper($user,'read',$userMessage)){
            return false;
        }
 
         return true;
    }

     /**
     * Determine whether the user can view listings of userMessages. NOTE that this method is not 
     * suitable for authorizing users outside of control panel
     *
     * @param  \BethelChika\Laradmin\User  $user
     * @return mixed
     */
     public function cpViews(User $user)
     {
         //Check at the table level
         if(!$this->perm->can($user,'table',$this->tableSourceId,'read')){
             return false;
         }

        //Check at the model level
        if(!$this->modelCheckHelper($user,'read')){
            return false;
        }
 
         return true;
        
     }

    /**
     * Determine whether the user can create userMessages in control panel.
     *
     * @param  \BethelChika\Laradmin\User  $user
     * @return mixed
     */
    public function cpCreate(User $user)
    {
        //Check at the table level
        if(!$this->perm->can($user,'table',$this->tableSourceId,'create')){
            return false;
        }

        //Check at the model level
        if(!$this->modelCheckHelper($user,'create')){
            return false;
        }
 
         return true;
    }

    /**
     * Determine whether the user can update the userMessage in control panel.
     *
     * @param  \BethelChika\Laradmin\User  $user
     * @param  \BethelChika\Laradmin\UserMessage  $userMessage
     * @return mixed
     */
    public function cpUpdate(User $user, UserMessage $userMessage)
    {
        //Check at the table level
        if(!$this->perm->can($user,'table',$this->tableSourceId,'update',$userMessage)){
            return false;
        }

        //Check at the model level
        if(!$this->modelCheckHelper($user,'update',$userMessage)){
            return false;
        }
 
         return true;
        
    }

    /**
     * Determine whether the user can delete the userMessage in control panel.
     *
     * @param  \BethelChika\Laradmin\User  $user
     * @param  \BethelChika\Laradmin\UserMessage $userMessage
     * @return mixed
     */
    public function cpDelete(User $user, UserMessage $userMessage)
    {
        //Check at table level
        if(!$this->perm->can($user,'table',$this->tableSourceId,'delete',$userMessage)){
            return false;
        }

        //Check at the model level
        if(!$this->modelCheckHelper($user,'delete',$userMessage)){
            return false;
        }
 
         return true;

    }

    
      
    /* *************************************************************************************/
    /* Authorising normal Users ************************************************************* */
    /* **********************************************************************/
    
    
    /**
     * Determine whether the user can view the UserMessage.
     *
     * @param  \BethelChika\Laradmin\User  $user
     * @param  \BethelChika\Laradmin\UserMessage $userMessage
     * @return mixed
     */
    public function view(User $user, UserMessage $userMessage)
    {
        
        return $this->isMy($user,$userMessage)  and 
        !$this->perm->isDisallowed($user,'table',$this->tableSourceId,'read',$userMessage) and
        !$this->modelCheckHelper($user,'read',$userMessage,'isDisallowed');
        
    }

     /**
     * Determines whether the user can view listings of userMessages. This function only checks that access 
     * is not explicitly denied on the message table and not access to individual message. Should not be 
     * used for authorising reading of the message content. It should only be used when the listng of 
     * message is limited to those of the logged in user, in which case the authorisation is already done by
     * limiting the message to those that belong to the current user
     *
     * @param  \BethelChika\Laradmin\User  $user
     * @return mixed
     */
     public function views(User $user)
     {
        return !$this->perm->isDisallowed($user,'table',$this->tableSourceId,'read') and
                !$this->modelCheckHelper($user,'read',null,'isDisallowed');
        
     }

    /**
     * Determine whether the user can create userMessagess.
     *
     * @param  \BethelChika\Laradmin\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return !$this->perm->isDisallowed($user,'table',$this->tableSourceId,'create') and
                !$this->modelCheckHelper($user,'create',null,'isDisallowed');;
    }

    /**
     * Determine whether the user can update the userMessage.
     *
     * @param  \BethelChika\Laradmin\User  $user
     * @param  \BethelChika\Laradmin\UserMessage  $userMessage
     * @return mixed
     */
    public function update(User $user, UserMessage $userMessage)
    {
        
        return $this->isMy($user,$userMessage)  and 
                !$this->perm->isDisallowed($user,'table',$this->tableSourceId,'update',$userMessage) and
                !$this->modelCheckHelper($user,'update',$userMessage,'isDisallowed');
        
    }

    /**
     * Determine whether the user can delete the userMessage.
     *
     * @param  \BethelChika\Laradmin\User  $user
     * @param  \BethelChika\Laradmin\UserMessage $userMessage
     * @return mixed
     */
    public function delete(User $user, UserMessage $userMessage)
    {
        
        return $this->isMy($user,$userMessage)  and 
                !$this->perm->isDisallowed($user,'table',$this->tableSourceId,'delete',$userMessage) and 
                !$this->modelCheckHelper($user,'delete',$userMessage,'isDisallowed');;

    }

    /*///////////////////////////////////////////////////////////////////////
    /*////////////Helprs////////////
    /*/////////////////////////////////////////////////////////
     /**
     * A helper for cheking permission at the model level
     *
     * @param User $user
     * @param string $action
     * @param Model $model The resource
     * @param string $method {'can','isDisallowed'}
     * @return boolean
     */
    private function modelCheckHelper(User $user, $action,Model $model=null,$method='can'){
        //Check at the model level
        $source=Source::where('type','model')->where('name',UserMessage::class)->first();
        if($source){
            //$access_string=Source::getTypeKey().':'.$source->id;
            if(str_is($method,'can')){
                if(!$this->perm->can($user,Source::class,$source->id,$action,$model)){
                    return false;
                }
            }else{
                if($this->perm->isDisallowed($user,Source::class,$source->id,$action,$model)){
                    return false;
                }
            }
        }

        //The user can do it
        if(str_is($method,'can')){
            return true;//
        }
        return false;//
   }
}
