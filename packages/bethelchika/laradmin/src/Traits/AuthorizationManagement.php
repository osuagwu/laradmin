<?php
namespace BethelChika\Laradmin\Traits;

/**
 * Brings easy access to some useful permission/authorization functions
 */
trait AuthorizationManagement
{
    public function permission(){
        return app('laradmin')->permission;
    }

     /**
     * Check if a user is a member of a  group with a given id
     * @param $group_id The id of the group
     *
     * @return boolean
     */
    public function isInGroup($group_id){
        return $this->permission()->isInGroup($this,$group_id);
    } 
    /**
     * Check if a user is a member of a named group
     * @param $group_name The name of the group
     *
     * @return boolean
     */
    public function isMemberOf($group_name){
        return $this->permission()->isMemberOf($this,$group_name);
    } 


    

   
}