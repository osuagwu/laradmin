<?php
namespace BethelChika\Laradmin\Traits;

use BethelChika\Laradmin\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;



trait UserManagement
{
    /**
    * Special groups ID of Banned USERGROUP
    * @var int
    */
    private $BANNED_USERGROUP_ID=1;
    
    /**
    * Special groups ID of ADMIN USERGROUP
    * @var int
    */
    private $ADMIN_USERGROUP_ID=2;

    
    /**
    * Control panel user ID. Control panel is a system user. 
    * The system performs tasks as this user when necce-
    * sary, e.g receives messages sent to the site. 
    * People shouldn't logon with this user
    */
    private $CP_ID=1;

    /**
    * The ID of super user who has all rights and cannot be banned or edited
    * @var int
    */
    private $SUPER_ID=2;
    
    /**
    * The ID of everyguest user
    * @var int
    */
    private $GUEST_ID=5; 

    /**
     * Returns the ID of super user
     *
     * @return int
     */
    function getSuperId(){
        return config('super_id',$this->SUPER_ID);
    }

    /**
     * Returns the ID of CP (the system user)
     *
     * @return int
     */
     function getCPId(){
        return config('cp_id',$this->CP_ID);
    }

    /**
     * Returns the ID of guest 
     *
     * @return int
     */
    function getGuestId(){
        return config('guest_id',$this->GUEST_ID);
    }

    /**
     * An alias of getCPId()
     *
     * @return int
     */
     function getSystemId(){
        return $this->getCPId();
    }
     /**
     * Returns the super User object
     *
     * @return \BethelChika\Laradmin\User
     */
    function getSuperUser(){
        return User::findOrFail($this->getSuperId());
    }

    /**
     * Returns the CP/System User object
     *
     * @return \BethelChika\Laradmin\User
     */
     function getCPUser(){
        return User::findOrFail($this->getCPId());
    }
    /**
     * An alias of getCPUser()
     *
     * @return \BethelChika\Laradmin\User
     */
     function getSystemUser(){
        return $this->getCPUser();
    }

    /**
     * Get the User for all guests to the site
     *
     * @return \BethelChika\Laradmin\User
     */
    function getGuestUser(){
        return User::findOrFail($this->getGuestId());
    }

     /**
     * Returns the ID of admin user gruop
     *
     * @return int
     */
    function getAdminUserGroupId(){
        return config('admin_usergroup_id',$this->ADMIN_USERGROUP_ID);
    } 

    /**
     * Returns the ID of banned user gruop
     *
     * @return int
     */
     function getBannedUserGroupId(){
        return config('banned_usergroup_id',$this->BANNED_USERGROUP_ID);
    } 


    

   
}