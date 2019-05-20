<?php

namespace BethelChika\Laradmin\WP\Policies;

use BethelChika\Laradmin\User;
//use BethelChika\Laradmin\Permission\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;
use BethelChika\Laradmin\Source;
use BethelChika\Laradmin\WP\Models\Page;
use BethelChika\Laradmin\Laradmin;

class PagePolicy
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
    public $tableAccessString;

    /**
    * Create a new policy instance.
  * @param Laradmin $laradmin
    * @return void
    */
    public function __construct(Laradmin $laradmin){
        $this->perm=$laradmin->permission;

        //Get table access info
        $temp=new Page();
        $this->tableAccessString=Source::getTableAccessString($temp);
        unset($temp);

        
    }
    public function before(User $user){
        
        if($user->id==$user->getSuperId())return true;
        
    }
    /**
     * Determine whether the user can view.
     *
     * @param  \App\User  $user 
     * @param Page $page
     * @return mixed
     */
    public function view(User $user,Page $page)
    { 
        //Check at the table level
        if($this->perm->isDisallowed($user,$this->tableAccessString,'read')){
            return false;
        }

        //Check at the model level
        $source=Source::where('type','model')->where('name',get_class($page))->first();
        if($source){
            $access_string=Source::getTypeKey().':'.$source->id;
            if($this->perm->isDisallowed($user,$access_string,'read')){
                return false;
            }
        }

        
        // Check at the page level
        $page_access_string=Source::getPageTypeKey().':'.$page->getKey();
        if($this->perm->isDisallowed($user,$page_access_string,'read')){
            return false;
        }


        return true;
    }

     /**
     * Determine whether the user can view listings.
     *
     * @param  \App\User  $user
     * @return mixed
     */
     public function views(User $user)
     {
        return false; //Only applicable in wordpress and controlpanel
     }

    /**
     * Determine whether the user can create.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        
        return false;//page creation is handled by wordpress
    }

    /**
     * Determine whether the user can update.
     *
     * @param  \App\User  $user
     * @param  Page
     * @return mixed
     */
    public function update(User $user, Page $page)
    {
        //Check at the table level
        if($this->perm->isDisallowed($user,$this->tableAccessString,'update')){
            return false;
        }

        
        //Check at the model level
        $source=Source::where('type','model')->where('name',get_class($page))->first();
        if($source){
            $access_string=Source::getTypeKey().':'.$source->id;
            if($this->perm->isDisallowed($user,$access_string,'update')){
                return false;
            }
        }

        
        // Check at the page level
        $page_access_string=Source::getPageTypeKey().':'.$page->getKey();
        if(!$this->perm->can($user,$page_access_string,'update')){
            return false;
        }


        return true;
    }

    /**
     * Determine whether the user can delete.
     *
     * @param  \App\User  $user
     * @param  Page
     * @return mixed
     */
    public function delete(User $user, Page $page)
    {
        return false;//page deletion is handled in wordpress
    }
}
