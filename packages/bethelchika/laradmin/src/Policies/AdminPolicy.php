<?php

namespace BethelChika\Laradmin\Policies;

use BethelChika\Laradmin\User;
//use BethelChika\Laradmin\Permission\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;
use BethelChika\Laradmin\Laradmin;



class AdminPolicy
{
    use HandlesAuthorization;
    /**
    * Permission object
    *
    */
    public $perm;
    
    /**
    * Create a new policy instance.
    * @param Laradmin $laradmin
    * @return void
    */
    public function __construct(Laradmin $laradmin){
        $this->perm=$laradmin->permission;
        //dd($perm);
    }
    

    /**
    * Check user can view control panel
    *@param User $user
    * @return boolean
    */
    function cp(User $user){
        //$name=Source::getRoutePrefixTypeKey().':/cp';
        return $this->perm->can($user,'route_prefix','/cp','read');
    }

    /**
     * Check if a user can administer, i.e if the user needs to be either the 
     * super or a none disabled or banned admin.
     *
     * @param User $user
     * @return boolean
     */
    public function administer(User $user){
        return $this->perm->can($user);
        //return $this->perm->canAdminister($user);
    }
    


}
