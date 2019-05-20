<?php

namespace BethelChika\Laradmin\Policies;


use BethelChika\Laradmin\User;
use BethelChika\Laradmin\UserGroup;
//use BethelChika\Laradmin\Permission\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;
use BethelChika\Laradmin\Source;
use BethelChika\Laradmin\Laradmin;
use Illuminate\Database\Eloquent\Model;

class UserGroupPolicy
{ 
    use HandlesAuthorization;
    /**
    * Permission object
    *
    */
    public $perm;

     /**
     * The key for accessing permission, comprising source type key and the table name
     *
     * @var string
     */
    public $tableAccessString;
    
    /**
    * Create a new policy instance.
    * @param Laradmin $laradmin
    * @return void
    */
    public function __construct(Laradmin $laradmin){
        $this->perm=$laradmin->permission;
        
        //Get table access info
        $temp=new UserGroup();
        $this->tableAccessString=Source::getTableAccessString($temp);
        unset($temp);
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
        //Check at the table level
        if(!$this->perm->can($user,$this->tableAccessString,'read',$userGroup)){
            return false;
        }

        //Check at the model level
        if(!$this->modelCheckHelper($user,'read',$userGroup)){
            return false;
        }
 
         return true;
    }

    /**
     * Determine whether the user can create userGroups.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //Check at the table level
        if(!$this->perm->can($user,$this->tableAccessString,'create')){
            return false;
        }

        //Check at the model level
        if(!$this->modelCheckHelper($user,'create')){
            return false;
        }
 
         return true;
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
        //Check at the table level
        $r= $this->perm->can($user,$this->tableAccessString,'update',$userGroup);

        //CHeck at the model level
        $m=$this->modelCheckHelper($user,'update',$userGroup);


        if($r and $m){
            //Do not allow the group mapping for Admin user group to be altered
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
        //Check at the table level
        $r= $this->perm->can($user,$this->tableAccessString,'delete',$userGroup);

        //CHeck at the model level
        $m=$this->modelCheckHelper($user,'delete',$userGroup);


        if($r and $m){
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
        
        //Check at the table level
        if(!$this->perm->can($user,$this->tableAccessString,'read')){
            return false;
        }

         //Check at the model level
         if(!$this->modelCheckHelper($user,'read')){
            return false;
        }
 
         return true;
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
     * @return boolean
     */
    private function modelCheckHelper(User $user, $action,Model $model=null){
        //Check at the model level
        $source=Source::where('type','model')->where('name',UserGroup::class)->first();
        if($source){
            $access_string=Source::getTypeKey().':'.$source->id;
            if(!$this->perm->can($user,$access_string,$action,$model)){
                return false;
            }
        }

        return true;
   }
}
