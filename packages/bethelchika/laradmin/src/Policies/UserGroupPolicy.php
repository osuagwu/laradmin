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
    public $tableSourceId;
    
    /**
    * Create a new policy instance.
    * @param Laradmin $laradmin
    * @return void
    */
    public function __construct(Laradmin $laradmin){
        $this->perm=$laradmin->permission;
        
        //Get table access info
        $temp=new UserGroup();
        $this->tableSourceId=Source::getTableSourceIdFromModel($temp);
        unset($temp);
    }
    /**
     * Determine whether the user can view the userGroup.
     *
     * @param  User  $user
     * @param  UserGroup  $userGroup
     * @return boolean
     */
    public function view(User $user, UserGroup $userGroup)
    {
        //Check at the table level
        if(!$this->perm->can($user,'table',$this->tableSourceId,'read',$userGroup)){
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
     * @param  User  $user
     * @return boolean
     */
    public function create(User $user)
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
     * Determine whether the user can update the userGroup.
     *
     * @param  User  $user
     * @param  UserGroup  $userGroup
     * @return boolean
     */
    public function update(User $user, UserGroup $userGroup)
    {
        //Check at the table level
        $r= $this->perm->can($user,'table',$this->tableSourceId,'update',$userGroup);

        //CHeck at the model level
        $m=$this->modelCheckHelper($user,'update',$userGroup);


        if($r and $m){
            //Do not allow the group details for Admin user group to be altered
            return !($user->getAdminUserGroupId()==$userGroup->id);
        }else{
            return false;
        }
    }

    /**
     * Determine whether the user can delete the userGroup.
     *
     * @param  User  $user
     * @param  UserGroup  $userGroup
     * @return boolean
     */
    public function delete(User $user, UserGroup $userGroup)
    {
        //Check at the table level
        $r= $this->perm->can($user,'table',$this->tableSourceId,'delete',$userGroup);

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
     * @param  User  $user
     * @return boolean
     */
     public function views(User $user)
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
            //$access_string=Source::getTypeKey().':'.$source->id;
            if(!$this->perm->can($user,Source::class,$source->id,$action,$model)){
                return false;
            }
        }

        return true;
   }
}
