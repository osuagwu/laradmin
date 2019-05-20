<?php

namespace BethelChika\Laradmin;

use Illuminate\Database\Eloquent\Model;
use BethelChika\Laradmin\Source\Traits\TypeKeys;
use BethelChika\Laradmin\Source\Traits\AccessString;

class Source extends Model
{
    use TypeKeys,AccessString;

    protected $guarded = ['id','created_at','updated_at'];

    /**
     * Default types of sources
     *
     * @var array
     */
    public static $DEFAULT_TYPES=[
        'table'=>'Table',
        'file'=>'File',
        'route'=>'Route',
        'route_prefix'=>'Route prefix',
        'url'=>'URL',
        'page'=>'Page',
        'model'=>'Model'
        ];
     
    /**
     * The source types that are allowed to be inserted into the sources table
     *
     * @var array
     */
    public static $UNGUARDED_DEFAULT_TYPES=[
        'file'=>'File',
        'url'=>'URL',
        'model'=>'Model',
        ];
    
    /**
     * A list of system tables that should be considered when settings things like permision etc
     *
     * @var array
     */
    public static $SYSTEM_TABLES=['migrations','password_resets','sqlite_sequence']; 

    //Relationship to user
    function user(){
        return $this->belongsTo('BethelChika\Laradmin\User');
    }

    /**
     * Get a list of all tables (excluding known system tables) with their associated db and connections, 
     *
     * @return array
     */
    public static function getAllTables(){
        
        $tables=[];
        //$with_exception=[];
        foreach(config('database.connections') as $con=>$con_value){
            $_tables=[];
            try{
                switch(strtolower($con)){
                    case 'mysql':
                        $_tables = \DB::connection($con)->select('SHOW TABLES'); // returns an array of 
                        break;
                    case 'sqlite':
                        $_tables= \DB::connection($con)->select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;");
                        break;
                    default:
                        continue;
                        //return view('laradmin::cp.sources', compact('tables'))->with('warning',' Could not list tables. Unknown database: '.env('DB_CONNECTION'));
                }
            }catch(\Illuminate\Database\QueryException $qex){
                //$with_exception[]=$con;
            }

            //attach connection to each table
            for($i=0;$i<count($_tables);$i++){
                $_tables[$i]->connection=$con;
                $_tables[$i]->connection_info=$con_value;

            }

            $tables=array_merge($tables,$_tables);
        }

        // Remove known system tables from the list
        $temp=[];
        foreach ($tables as $table){
            if(!in_array($table->name,static::$SYSTEM_TABLES)){
                $table->label=ucfirst(str_replace('_',' ',$table->name));
                $temp[]=$table;
            }
        } 
        $tables=$temp;
        return $tables;
    }


}
