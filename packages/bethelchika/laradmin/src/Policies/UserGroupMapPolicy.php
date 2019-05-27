<?php

namespace BethelChika\Laradmin\Policies;

use BethelChika\Laradmin\User;
use BethelChika\Laradmin\UserGroupMap;
//use BethelChika\Laradmin\Permission\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;
use BethelChika\Laradmin\Source;
use BethelChika\Laradmin\Laradmin;
use Illuminate\Database\Eloquent\Model;

class UserGroupMapPolicy
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
        $temp=new UserGroupMap();
        $this->tableSOurceId=Source::getTableSourceIdFromModel($temp);
        unset($temp);
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
         

        //Check at the table level
        $r= $this->perm->can($user,'table',$this->tableSourceId,'update');

        //CHeck at the model level
        $m=$this->modelCheckHelper($user,'update',$userToMap);

        if($r and $m){
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
            //$access_string=,$source->id;
            if(!$this->perm->can($user,Source::class,$source->id,$action,$model)){
                return false;
            }
        }

        return true;
   }
}
