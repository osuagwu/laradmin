<?php

namespace BethelChika\Laradmin\Policies;

use BethelChika\Laradmin\User;
//use BethelChika\Laradmin\Permission\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;
use BethelChika\Laradmin\Source;
use BethelChika\Laradmin\Laradmin;
use Illuminate\Database\Eloquent\Model;

class UserPolicy
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
        $temp_user=new User;
        $this->tableSourceId=Source::getTableSourceIdFromModel($temp_user);
        unset($temp_user);
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
        //Check at the table level
        if(!$this->perm->can($user,'table',$this->tableSourceId,'read',$userToView)){
            return false;
        }

        
        //Check at the model level
        if(!$this->modelCheckHelper($user,'read',$userToView)){
            return false;
        }

        return true;
    }

     /**
     * Determine whether the user can view listings of user.
     *
     * @param  \App\User  $user
     * @return mixed
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

    /**
     * Determine whether the user can create users.
     *
     * @param  \App\User  $user
     * @return mixed
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
     * Determine whether the user can update the user.
     *
     * @param  \App\User  $user
     * @param  \App\User  $userToUpdate
     * @return mixed
     */
    public function update(User $user, User $userToUpdate)
    {
        //$perm=new Permission;

        
        // Check at the table level
        $r= $this->perm->can($user,'table',$this->tableSourceId,'update',$userToUpdate);

        //Check at the model level
        $m=$this->modelCheckHelper($user,'update',$userToUpdate);
        
        if($r and $m){
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
        

        //Check at the table level
        $r= $this->perm->can($user,'table',$this->tableSourceId,'delete',$userToDelete);
        
        //Check at the model level
        $m=$this->modelCheckHelper($user,'delete',$userToDelete);
        
        
        if($r and $m){ 
            //Do not allow the super/system(CP) to be deleted
            return !($user->getSuperId()==$userToDelete->id or $user->getCPId()==$userToDelete->id);
        }else{
            return false;
        }
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
        $source=Source::where('type','model')->where('name',User::class)->first();
        if($source){
            //$access_string=Source::getTypeKey().':'.$source->id;
            if(!$this->perm->can($user,Source::class,$source->id,$action,$model)){
                return false;
            }
        }

        return true;
   }
}
