<?php

namespace BethelChika\Laradmin\Policies;

use BethelChika\Laradmin\User;
//use BethelChika\Laradmin\Permission\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;
use BethelChika\Laradmin\Laradmin;
use BethelChika\Laradmin\Source;

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
    * @param Laradmin $laradmin
    * @return void
    */
    public function __construct(Laradmin $laradmin){
        $this->perm=$laradmin->permission;
        //dd($perm);
    }
    

    /**
    * .Check user can view control panel
    *@param App\User $user
    * @return boolean
    */
    function view(User $user){
        //$name=Source::getRoutePrefixTypeKey().':/cp';
        return $this->perm->can($user,'route_prefix','/cp','read');
    }
}
