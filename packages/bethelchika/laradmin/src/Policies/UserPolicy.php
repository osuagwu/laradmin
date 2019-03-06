<?php

namespace BethelChika\Laradmin\Policies;

use BethelChika\Laradmin\User;
use BethelChika\Laradmin\Permission\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;
    /**
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
        //dd($perm);
    }
    /**
     * Determine whether the user can view the user.
     *
     * @param  \App\User  $user
     * @param  \App\User $userToView
     * @return mixed
     */
    public function view(User $user, User $userToView)
    {
        //$perm=new Permission;
        return $this->perm->can($user,'table:users','read',$userToView);
    }

     /**
     * Determine whether the user can view listings of user.
     *
     * @param  \App\User  $user
     * @return mixed
     */
     public function views(User $user)
     {//dd($user);
        //$perm=new Permission;
        return $this->perm->can($user,'table:users','read');
     }

    /**
     * Determine whether the user can create users.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //$perm=new Permission;
        return $this->perm->can($user,'table:users','create');
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param  \App\User  $user
     * @param  \App\User  $userToUpdate
     * @return mixed
     */
    public function update(User $user, User $userToUpdate)
    {
        //$perm=new Permission;

        

        $r= $this->perm->can($user,'table:users','update',$userToUpdate);
        if($r){
            //Do not allow the super to be updated
            return !($user->getSuperId()==$userToUpdate->id);
        }else{
            //If a user is trying to edit herself and she is not 'super' then allow
            //if($user->id==$userToUpdate->id and ($perm->getSuperId()!=$userToUpdate->id)){
            //    return true;
            //}
            return false;
        }
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param  \App\User  $user
     * @param  \App\User $userToDelete
     * @return mixed
     */
    public function delete(User $user, User $userToDelete)
    {
        

        //return true;
        //$perm=new Permission;
        $r= $this->perm->can($user,'table:users','delete',$userToDelete);
        //dd($user);
        if($r){ 
            //Do not allow the super/system(CP) to be deleted
            return !($user->getSuperId()==$userToDelete->id or $user->getCPId()==$userToDelete->id);
        }else{
            return false;
        }
    }
}
