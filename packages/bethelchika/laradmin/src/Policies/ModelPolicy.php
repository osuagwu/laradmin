<?php

namespace BethelChika\Laradmin\Policies;

use BethelChika\Laradmin\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use BethelChika\Laradmin\Source;
use BethelChika\Laradmin\Laradmin;
use Illuminate\Database\Eloquent\Model;

class ModelPolicy
{
    /**
     * This policy class provides a quick general policy for a model and can be used when there 
     * is no need to create a specific policy class for a model. 
     * 
     * Note that this policy class should be used when a user should only have access 
     * to a model if the user have explicit permission for source type=model for this model or
     * owns the model or have admin powers. If none of these applies access will be denied. So 
     * this policy is not suitable for models that everyone is allowed to access without 
     * explicit permission. User access can be controlled for a particular model by creating 
     * permission using the source type=model in the control panel.
    */


    use HandlesAuthorization;
    /**
    * Permission object
    * \BethelChika\Laradmin\Permission\Permission
    */
    public $perm;


    /**
    * Create a new policy instance.
    * @param Laradmin $laradmin
    * @return void
    */
    public function __construct(Laradmin $laradmin){
        $this->perm=$laradmin->permission;

    }
    /**
     * Determine whether the user can create the model. Note that this method will create 
     * an instance of the model. SO do not use this method if this could lead to error 
     * or if you do not want the creation of the instance.
     *
     * @param  User  $user
     * @param  string $class_name
     * @return boolean
     */
    public function create(User $user, string $class_name)
    {        
        //Check at the model level
        if(!$this->modelCheckHelper($user,'create',null,$class_name)){
            return false;
        }
        
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Model $model
     * @return boolean
     */
    public function view(User $user, Model $model)
    {
        
        //Check at the model level
        if(!$this->modelCheckHelper($user,'read',$model)){
            return false;
        }

        return true;
    }

      /**
     * Determine whether the user can list the model. Note that this method will create 
     * an instance of the model. SO do not use this method if this could lead to error 
     * or if you do not want the creation of the instance.
     *
     * @param  User  $user
     * @param  string $class_name
     * @return boolean
     */
    public function views(User $user, string $class_name)
    {
        
        //Check at the model level
        if(!$this->modelCheckHelper($user,'read',null,$class_name)){
            return false;
        }
        

        return true;
    }


    

    /**
     * Determine whether the user can update the user.
     *
     * @param  User  $user
     * @param  Model $model
     * @return boolean
     */
    public function update(User $user, Model $model)
     {
        //Check at the model level
        $m=$this->modelCheckHelper($user,'update',$model);
        
        if($m){
            //Do not allow the super to be updated by any one else but super. 
            // Note the code above works but we are using the ifs below for easy readability.
            if($model instanceof \BethelChika\Laradmin\User) {
                if ($user->getSuperId()==$model->id) {
                    if ($model->id!=$user->id) {
                        return false;//Super can only be updated by super.
                    }
                }
            }
            return true;
        }else{
            
            return false;
        }
    }

    /**
     * Determine whether the user can delete the model.
     * 
     *
     * @param  User  $user
     * @param  Model $model
     * @return boolean
     */
    public function delete(User $user, Model $model)
    {        
        //Check at the model level
        $m=$this->modelCheckHelper($user,'delete',$model);
        
        
        
        if($m){
            //Do not allow the super to be updated by any one else but super. 
            // Note the code above works but we are using the ifs below for easy readability.
            if($model instanceof \BethelChika\Laradmin\User) {
                if ($user->getSuperId()==$model->id) {
                    if ($model->id!=$user->id) {
                        return false;//Super can only be updated by super.
                    }
                }
            }
            return true;
        }else{
            
            return false;
        }
    }

   

    /*///////////////////////////////////////////////////////////////////////
    /*////////////Helpers////////////
    /*/////////////////////////////////////////////////////////
    */
    
     /**
     * A helper for checking permission at the model level. 
     *
     * @param User $user
     * @param string $action
     * @param Model $model The resource. Must be provided id $class_name is null
     * @param $class_name The resource class name. Must be supplied if $model is null
     * @return boolean
     */
    private function modelCheckHelper(User $user, $action,Model $model=null,$class_name=null){

        if(!$class_name){
            if($model){
               $class_name=get_class($model); 
            }else{
                return false;
            }
            
        }
        
        //Check at the model level
        $source=Source::where('type','model')->where('name',$class_name)->first();
        if($source){
            //$access_string=Source::getTypeKey().':'.$source->id;
            if(!$this->perm->can($user,Source::class,$source->id,$action,$model)){
                return false;
            }
        }else{
            // There is no Source of type model defined so perform a general check instead.
            if(!$this->perm->can($user,null,null,[],$model)){
                return false;
            }
        }

        return true;
   }
}
