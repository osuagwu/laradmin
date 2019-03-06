<?php

namespace BethelChika\Laradmin\Policies;

use BethelChika\Laradmin\User;
use BethelChika\Laradmin\UserMessage;
use BethelChika\Laradmin\Permission\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;
 
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
    * Create a new policy instance.
    *
    * @return void
    */
    public function __construct(Permission $perm){
        $this->perm=$perm;
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
        return $this->perm->can($user,'table:user_messages','read',$userMessage);
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
        return $this->perm->can($user,'table:user_messages','read');
        
     }

    /**
     * Determine whether the user can create userMessages in control panel.
     *
     * @param  \BethelChika\Laradmin\User  $user
     * @return mixed
     */
    public function cpCreate(User $user)
    {
        //$perm=new Permission;
        return $this->perm->can($user,'table:user_messages','create');
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
        
        return $this->perm->can($user,'table:user_messages','update',$userMessage);
        
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
        
        return $this->perm->can($user,'table:user_messages','delete',$userMessage);

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
        
        return $this->isMy($user,$userMessage)  and !$this->perm->isDisallowed($user,'table:user_messages','read',$userMessage);
        
    }

     /**
     * Determine whether the user can view listings of userMessages. This function only checks that access 
     * is not explicitly denied on the message table and not access to individual message. Should not be used for 
     * authoissing reading of message content. It should only be used when the listng of message is
     * limited to those of the logged in user, in which case the authorisation is already done by
     * limiting the message to those that belong to the current user
     *
     * @param  \BethelChika\Laradmin\User  $user
     * @return mixed
     */
     public function views(User $user)
     {
        return !$this->perm->isDisallowed($user,'table:user_messages','read');
        
     }

    /**
     * Determine whether the user can create userMessagess.
     *
     * @param  \BethelChika\Laradmin\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return !$this->perm->isDisallowed($user,'table:user_messages','create');
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
        
        return $this->isMy($user,$userMessage)  and !$this->perm->isDisallowed($user,'table:user_messages','update',$userMessage);
        
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
        
        return $this->isMy($user,$userMessage)  and !$this->perm->isDisallowed($user,'table:user_messages','delete',$userMessage);

    }
}
