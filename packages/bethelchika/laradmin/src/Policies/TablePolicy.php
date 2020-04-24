<?php

namespace BethelChika\Laradmin\Policies;

use BethelChika\Laradmin\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use BethelChika\Laradmin\Source;
use BethelChika\Laradmin\Laradmin;

class TablePolicy
{
    /**
     * This policy class is similar to ModelPolicy (See ModelPolicy::class for more details) but 
     * for tables. It denies by default unless explicit access is given or user has admin 
     * powers. Unlike ModelPolicy it is of course not possible to have instance of a table 
     * owned by a user; so access cannot be granted in this way for TablePolicy.
     * Example usages:
     * [In a controller]
     * $this->authorize('table.create','user_messages');
     * $this->authorize('table.create',['user_messages','my_connection']);//Specify the connect
     * 
     * [Using gate facade]
     * Gate::check('table.create','user_messages');
     * Gate::authorize('table.delete',['user_messages','my_connection']);
     * 
     * etc.
     */


    use HandlesAuthorization;
    /**
    * Permission object
    * BethelChika\Laradmin\Permission\Permission
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
     * Determine whether the user can create . 
     * @param User $user
     * @param string $table_name
     * @param string $database_connection If null is given the default value in the config is used.
     * @return boolean
     */
    public function create(User $user, string $table_name,string $database_connection=null)
    {
        
        return $this->tableCheckHelper($user,'create', $table_name,$database_connection);
        
    }

     /**
     * Determine whether the user can view . 
     * @param User $user
     * @param string $table_name
     * @param string $database_connection If null is given the default value in the config is used.
     * @return boolean
     */
    public function view(User $user, string $table_name,string $database_connection=null)
    {
        return $this->tableCheckHelper($user,'read', $table_name,$database_connection);
        
    }


    /**
     * Check if user can list 
     *
     * @param User $user
     * @param string $table_name
     * @param string $database_connection If null is given the default value in the config is used.
     * @return boolean
     */
    public function views(User $user, string $table_name,string $database_connection=null)
    {
        return $this->tableCheckHelper($user,'read', $table_name,$database_connection);
        
    }

    /**
     * Check if user can update
     *
     * @param User $user
     * @param string $table_name
     * @param string $database_connection If null is given the default value in the config is used.
     * @return boolean
     */
    public function update(User $user, string $table_name,string $database_connection=null)
    {
        return $this->tableCheckHelper($user,'update', $table_name,$database_connection);
        
    }

    /**
     * Check if user can delete
     *
     * @param User $user
     * @param string $table_name
     * @param string $database_connection If null is given the default value in the config is used.
     * @return boolean
     */
    public function delete(User $user, string $table_name,string $database_connection=null)
    {
        return $this->tableCheckHelper($user,'delete', $table_name,$database_connection);
        
    }

    
    /**
     * A general helper fr checking tables
     *
     * @param User $user
     * @param string $action
     * @param string $table_name
     * @param string $database_connection If null is given the default value in the config is used.
     * @return boolean
     */
    private function tableCheckHelper(User $user, string $action, string $table_name,$database_connection=null)
    {
        if (is_null($database_connection)) {
            $database_connection=config('database.default');
        }

        $database=config('database.connections.'.$database_connection.'.database');
        $table_prefix=config('database.connections.'.$database_connection.'.prefix');
        

        // If any detail is missing  deny
        if(!$database_connection or !$database or $table_prefix){
            return false;
        }

        //Get table access info
        $tableSourceId=Source::getTableSourceId($database_connection, $database, $table_prefix, $table_name);
        
        //Check at the table level
        if (!$this->perm->can($user, 'table', $tableSourceId, $action)) {
            return false;
        }
        return true;
    }
}
