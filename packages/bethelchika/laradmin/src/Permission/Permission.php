<?php

namespace BethelChika\Laradmin\Permission;

use BethelChika\Laradmin\User;
use BethelChika\Laradmin\UserGroup;
use BethelChika\Laradmin\Permission as PermissionModel;
use Illuminate\Support\Collection;
use BethelChika\Laradmin\Events\AuthRestrict;
use Illuminate\Database\Eloquent\Model;

class Permission 
{  
    use Traits\Helpers;
    
    

    /**
     * Construct a new permission.
     * @return void
     */
     public function __construct(){

     }

     /**
      * Peform general checks.
      * @param User $user The user asking for permisiion
      * @return boolean
      */
      private function before(User $user){
            
            if($user->hasLoginRestrictions()){
                
                //Fire off event
               event(new AuthRestrict($user)); 
               return false;
            }
            return true;
      }

    /**
     * Find the collection of group models a user belongs to.
     *
     * @param \BethelChika\Laradmin\User $user
     * @return \Illuminate\Support\Collection
     */
    private function getUserGroups(User $user){
        $userGroups=[];
        foreach($user->userGroupMap as $ugm){
            $userGroups[]=UserGroup::find($ugm->user_group_id);
        }
        return collect($userGroups);
    }

     /**
     * For a givn user return permission string
     *
     * @param BethelChika\Laradmin\User $user
     * @param string $source_type
     * @param string $source_id
     * @return mixed
     */
     private function getUserPermissions(User $user,$source_type,$source_id){
        $select=['create','read','update','delete'];
        $perm=PermissionModel::where('user_id',$user->id)
        ->where('source_type',$source_type)
        ->where('source_id',$source_id)
        ->select($select)->first();
        
        if($perm)
            return $perm->create.$perm->read.$perm->update.$perm->delete;
        else return false;
    }

    /**
     * For a given userGroup return permission string
     *
     * @param BethelChika\Laradmin\UserGroup $userGroup
     * @param string $source_type
     * @param string $source_id
     * @return mixed
     */
     private function getUserGroupPermissions(User $user,$source_type,$source_id){
        $select=['create','read','update','delete'];
        $perm=PermissionModel::where('user_group_id',$user->id)
        ->where('source_type',$source_type)
        ->where('source_id',$source_id)
        ->select($select)->first();
        if($perm)
            return $perm->create.$perm->read.$perm->update.$perm->delete;
        else return false;
    }



     /**
     * For a givn user return permission string for a perticular action
     *
     * @param BethelChika\Laradmin\User $user
     * @param string $source_type
     * @param string $source_id
     * @param string $action Example Any of 'create','read', 'update' and 'delete'
     * @return mixed
     */
     private function getUserPermission(User $user,$source_type,$source_id,$action){
        $perm=PermissionModel::where('user_id',$user->id)
        ->where('source_type',$source_type)
        ->where('source_id',$source_id)
        ->select($action)->first();
        //dd($resource);
        if ($perm)
            return $perm->$action;
        else return false;
    }
    /**
     * For a given userGroup return permission string for a perticular action
     *
     * @param BethelChika\Laradmin\UserGroup $userGroup
     * @param string $source_type
     * @param string $source_id
     * @param string $action Example Any of 'create','read', 'update' and 'delete'
     * @return string
     */
     private function getUserGroupPermission(UserGroup $userGroup,$source_type,$source_id,$action){
        $userGroups=[];
        $perm=PermissionModel::where('user_group_id',$userGroup->id)
        ->where('source_type',$source_type)
        ->where('source_id',$source_id)
        ->select($action)->first();
        //dd($perm);
        if ($perm)
            return $perm->$action;
        else return false;
    }


    /**
     * Checks if there is at least one entry for a given resource
     *
     * @param string $source_type
     * @param string $source_id
     * @param string $action(CURRENTLY UNUSED) Example Any of 'create','read', 'update' and 'delete' //TODO: Note that the $action here is actually not that important, but it could become required in the future
     * @return boolean
     */
    public function hasEntry($source_type,$source_id,$action=null){
        return !!PermissionModel::where('source_type',$source_type)
        ->where('source_id',$source_id)->first();
        //->select($action)->first();
    }

      /**
     * Checks if there is at least one entry denied against any user/group for a given resource and action
     *
     * @param string $source_type
     * @param string $source_id
     * @param string $action Example Any of 'create','read', 'update' and 'delete'
     * @return boolean
     */
    public function hasDenyEntry($source_type,$source_id,$action){
        return !!PermissionModel::where('source_type',$source_type)
        ->where('source_id',$source_id)
        ->where($action,0)
        ->select($action)->count();
    }

     /**
     * Check if a user has special super powers
     *
     * @param BethelChika\Laradmin\User $user
     * @return boolean
     */
    private function isSuper(User $user){
        
        if ($user->id==$user->getSuperId()){
            return true;
        }
        
       
        return false;
    }
    /**
     * CHeck if a user is admin
     *
     * @param BethelChika\Laradmin\User $user
     * @param Collection <BethelChika\Laradmin\UserGroup> $userGroup
     * @return boolean
     */
    private function isAdmin(User $user,Collection $userGroups){
        foreach($userGroups as $userGroup){
            if ($userGroup->id==$user->getAdminUserGroupId()){
                return true;
            }

        }
    }

    /**
     * CHeck if a user is banned depending on group membership
     *
     * @param BethelChika\Laradmin\User $user
     * @param Collection <BethelChika\Laradmin\UserGroup> $userGroup
     * @return boolean
     */
     private function isBanned(User $user,Collection $userGroups){
        foreach($userGroups as $userGroup){
            if ($userGroup->id==$user->getBannedUserGroupId()){
                return true;
            }

        }
        return false;
    }

    /**
     * Check if a User is infact the supplied model
     *
     * @param BethelChika\Laradmin\User $user
     * @param Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
     private function isMe(User $user,Model $model){
        //Check if this user is the same as the model () and that the model is not Super user (prevent super from altering/viewing itself; i know its a little strange that it cannot even alter/view itself)
        if($user->is($model) and str_is(get_class($user),get_class($model))){//it may not be needed but we also test that the $model have the same class as $user using get_class
            //if ($user->id==$model->id){
                if($user->id!=$user->getSuperId()){
                    return true;
                }
            //}
        }

        
        return false;
    }

    /**
     * CHeck if a User owns a model
     *
     * @param BethelChika\Laradmin\User $user
     * @param Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    private function isMy(User $user,Model $model){
        

        //Check if this object is created by this user
        try { //Try to access a property that might not exist
            $myId=$model->creator_user_id;

            if ($user->id==$myId){
                return true;
            }
        }catch(Exception $ex){

        }

        try { //Try to access a property that might not exist
            $myId=$model->user_id;

            if ($user->id==$myId){
                return true;
            }
        }catch(Exception $ex){

        }

        
        return false;
    }

    /**
     * Check if the specified user is disabled. Note that if the user is 
     * actually disabled then he/she might be forced to logout before 
     * this function gets to execute. So this function might never 
     * get to be be called unless the user is not forced to log 
     * out.
     *
     * @param BethelChika\Laradmin\User $user
     * @return boolean
     */
     private function isDisabled(User $user){

        if ($user->is_active==0){
            return true;
        }
        return false;
    }

     /**
     * CHeck if a User is allowed to perform a given action. Unless the user 
     * is Super, non-explictly disallowed admin or owner, then given user 
     * must have explicit permission to access 
     *
     * @param BethelChika\Laradmin\User $user
     * @param string $source_type
     * @param string $source_id
     * @param string $action Example Any of 'create','read', 'update' and 'delete'
     * @param Illuminate\Database\Eloquent\Model $model
     * @return boolean True if the user can do the given action on the resource
     */
    public function can(User $user,$source_type,$source_id,$action,Model $model=null){
        //first and foremost
        if(!$this->before($user)){
            return false;
        }
        
        $cans=0;//Keep count of the times the user is explicitly allowed
        $userGroups=$this->getUserGroups($user);
        //dd($userGroups);

        // Check if user has super powers etc
        if ($this->isSuper($user)){
            return true;
        }
        
        
        //_______ User seem not to have special powers//////////////
       // ____________________________________________________________

        // Is the user disabled. A disabbled user might be forced to logout,
        // in which case we will not get here, but we still make sure that
        // we stop him.
        if($this->isDisabled($user)){
            return false;
        }

        // If the user is banned then do not allow
        if($this->isBanned($user,$userGroups)){
            return false;
        }

        
        //Check for personal restriction
        $permStr=$this->getUserPermission($user,$source_type,$source_id,$action);
        //dd($permStr);
        if($permStr!==false) {
            if(!strcmp($permStr,'0')){
                return false;
            }else{
                $cans++;
            }
        }

        //CHeck for user restriction in the groups
        foreach($userGroups as $userGroup){
            $permStr=$this->getUserGroupPermission($userGroup,$source_type,$source_id,$action);
           if($permStr!==false) {//dd($userGroup);//////////////////////////////////////////
                if(!strcmp($permStr,'0')){
                    return false;
                }else{
                    $cans++;
                }
            }
        }
        
        //Check if the $resource is this user and allow user access
        if($model){
            if($this->isMe($user,$model)){
                return true;
                
            }
        }

        //Check if the $resource is owned by user and allow user access
        if($model){
            if($this->isMy($user,$model)){
                return true;
            }
        }

        //So the user was not restricted anywhere; but is she actually permitted anywhere?
        if(!$cans){
            //OK the user is also not permitted anywhere as well as not restricted; but is the user an admin
            if (!$this->isAdmin($user,$userGroups)){
                return false;
            }
        }

        //user is allwed or is admin who is not restricted
        return true;

    }

    

    /**
     * Check if a User is explicitly disallowed to perform a given action
     *
     * @param BethelChika\Laradmin\User $user
     * @param string $source_type
     * @param string $source_id
     * @param string $action Example Any of 'create','read', 'update' and 'delete'
     * @param Illuminate\Database\Eloquent\Model $model TODO:[THIS PARAM IS NOT USED YET]
     * @return boolean True is the user is disallowed
     */
     public function isDisallowed(User $user,$source_type,$source_id,$action,Model $model=null){
        //first and foremost
        if(!$this->before($user)){
            return true;
        }
        
        //TODO:: Add $this->before here also.
        
        //$cans=0;//Keep count of the times the user is explicitly allowed
        $userGroups=$this->getUserGroups($user);
       

        

        
        //Is the user disabled
        if($this->isDisabled($user)){
            return true;
        }

        //If the user is banned then do not allow
        if($this->isBanned($user,$userGroups)){
            return true;
        }

       
        //Check for personal restriction
        $permStr=$this->getUserPermission($user,$source_type,$source_id,$action);
        //dd($permStr);
        if($permStr!==false) {
            if(!strcmp($permStr,'0')){
                return true;
            }else{
                //$cans++;
            }
        }

        //CHeck for user restriction in the groups
        foreach($userGroups as $userGroup){
            $permStr=$this->getUserGroupPermission($userGroup,$source_type,$source_id,$action);
           if($permStr!==false) {//dd($userGroup);//////////////////////////////////////////
                if(!strcmp($permStr,'0')){
                    return true;
                }else{
                    //$cans++;
                }
            }
        }
        

        //user is not restricted
        return false;

    }

    
    
}