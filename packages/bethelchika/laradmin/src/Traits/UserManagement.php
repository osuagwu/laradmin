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
    private static $BANNED_USERGROUP_ID=1;
    
    /**
    * Special groups ID of ADMIN USERGROUP
    * @var int
    */
    private static $ADMIN_USERGROUP_ID=2;

    
    /**
    * Control panel user ID. Control panel is a system user. 
    * The system performs tasks as this user when necce-
    * sary, e.g receives messages sent to the site. 
    * People shouldn't logon with this user
    */
    private static $CP_ID=1;

    /**
    * The ID of super user who has all rights and cannot be banned or edited
    * @var int
    */
    private static $SUPER_ID=2;
    
    /**
    * The ID of everyguest user
    * @var int
    */
    private static  $GUEST_ID=5; 

    /**
     * Returns the ID of super user
     *
     * @return int
     */
    public static function getSuperId(){
        return config('super_id',static::$SUPER_ID);
    }

    /**
     * Returns the ID of CP (the system user)
     *
     * @return int
     */
    public static function getCPId(){
        return config('cp_id',static::$CP_ID);
    }

    /**
     * Returns the ID of guest 
     *
     * @return int
     */
    public static function getGuestId(){
        return config('guest_id',static::$GUEST_ID);
    }

    /**
     * An alias of getCPId()
     *
     * @return int
     */
    public  static function getSystemId(){
        return static::getCPId();
    }
     /**
     * Returns the super User object
     *
     * @return \BethelChika\Laradmin\User
     */
    public static function getSuperUser(){
        return User::findOrFail(static::getSuperId());
    }

    /**
     * Returns the CP/System User object
     *
     * @return \BethelChika\Laradmin\User
     */
    public  static function getCPUser(){
        return User::findOrFail(static::getCPId());
    }
    /**
     * An alias of getCPUser()
     *
     * @return \BethelChika\Laradmin\User
     */
    public static function getSystemUser(){
        return static::getCPUser();
    }

    /**
     * Get the User for all guests to the site
     *
     * @return \BethelChika\Laradmin\User
     */
    public static function getGuestUser(){
        return User::findOrFail(static::getGuestId());
    }

     /**
     * Returns the ID of admin user gruop
     *
     * @return int
     */
    public static function getAdminUserGroupId(){
        return config('admin_usergroup_id',static::$ADMIN_USERGROUP_ID);
    } 

    /**
     * Returns the ID of banned user gruop
     *
     * @return int
     */
    public static function getBannedUserGroupId(){
        return config('banned_usergroup_id',static::$BANNED_USERGROUP_ID);
    } 


    

   
}