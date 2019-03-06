<?php

namespace BethelChika\Laradmin\Policies;

use BethelChika\Laradmin\User;
use BethelChika\Laradmin\UserGroupMap;
use BethelChika\Laradmin\Permission\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserGroupMapPolicy
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
     * Determine whether the user can view the userGroupMap.
     *
     * @param  \App\User  $user
     * @param  \App\UserGroupMap  $userGroupMap
     * @return mixed
     */
    public function view(User $user, UserGroupMap $userGroupMap)
    {
        //
    }

    /**
     * Determine whether the user can create userGroupMaps.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the userGroupMap.
     *
     * @param  \App\User  $user
     * @param  \App\UserGroupMap  $userGroupMap
     * @return mixed
     */
    public function update(User $user, UserGroupMap $userGroupMap)
    {
        //
    }

     /**
     * Determine whether the user can update the userGroupMap.
     *
     * @param  \App\User  $user
     * @return mixed
     */
     public function updates(User $user,$userToMap)
     {
         

        //$perm=new Permission;
        $r= $this->perm->can($user,'table:user_group_maps','update');
        if($r){
            //Do not allow the group mapping of super to be alttered
            return !($user->getSuperId()==$userToMap->id);
        }else{
            return false;
        }
     }

    /**
     * Determine whether the user can delete the userGroupMap.
     *
     * @param  \App\User  $user
     * @param  \App\UserGroupMap  $userGroupMap
     * @return mixed
     */
    public function delete(User $user, UserGroupMap $userGroupMap)
    {
        //
    }
}
