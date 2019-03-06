<?php

namespace BethelChika\Laradmin\Policies;

use BethelChika\Laradmin\User;
use BethelChika\Laradmin\Permission\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;

class ControlPanelPolicy
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
    * .Check user can view control panel
    *@param App\User $user
    * @return boolean
    */
    function view(User $user){
        //var_dump($user);
        //dd($user);
        //$perm=new Permission;
        return $this->perm->can($user,'site:cp','read');
    }
}
