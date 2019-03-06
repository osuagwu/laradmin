<?php

namespace BethelChika\Laradmin\Policies;


use BethelChika\Laradmin\User;
use BethelChika\Laradmin\UserGroup;
use BethelChika\Laradmin\Permission\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserGroupPolicy
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
     * Determine whether the user can view the userGroup.
     *
     * @param  \App\User  $user
     * @param  \App\UserGroup  $userGroup
     * @return mixed
     */
    public function view(User $user, UserGroup $userGroup)
    {
        //return true;
        //$perm=new Permission;
        return $this->perm->can($user,'table:user_groups','read',$userGroup);
    }

    /**
     * Determine whether the user can create userGroups.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //$perm=new Permission;
        return $this->perm->can($user,'table:user_groups','create');
    }

    /**
     * Determine whether the user can update the userGroup.
     *
     * @param  \App\User  $user
     * @param  \App\UserGroup  $userGroup
     * @return mixed
     */
    public function update(User $user, UserGroup $userGroup)
    {
        //return true;
        //$perm=new Permission;
        $r= $this->perm->can($user,'table:user_groups','update',$userGroup);
        if($r){
            //Do not allow the group mapping or Amin user group to be altered
            return !($user->getAdminUserGroupId()==$userGroup->id);
        }else{
            return false;
        }
    }

    /**
     * Determine whether the user can delete the userGroup.
     *
     * @param  \App\User  $user
     * @param  \App\UserGroup  $userGroup
     * @return mixed
     */
    public function delete(User $user, UserGroup $userGroup)
    {
        //return true;
        //$perm=new Permission;
        $r= $this->perm->can($user,'table:user_groups','delete',$userGroup);
        if($r){
            //Do not allow the admin/banned user group to be deleted
            return !($user->getAdminUserGroupId()==$userGroup->id or $user->getBannedUserGroupId()==$userGroup->id);
        }else{
            return false;
        }
    }

    /**
     * Determine whether the user can list the userGroup.
     *
     * @param  \App\User  $user
     * @return mixed
     */
     public function views(User $user)
     {
        
        //$perm=new Permission;
        return $this->perm->can($user,'table:user_groups','read');
     }
}
