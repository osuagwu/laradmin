<?php

namespace  BethelChika\Laradmin\Http\Controllers\CP;

use Illuminate\Http\Request;

use DB;
use BethelChika\Laradmin\MainPermission;
use Illuminate\Support\Facades\Gate;
use BethelChika\Laradmin\Http\Controllers\CP\Controller; //NOTE: This is explicitly imported to avoid wrong use of a controller if this file is coppied elsewhere
class SourceController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
     public function __construct()
     {
        $this->middleware('auth');
        parent::__construct();
     }


   protected $systemTables=['migrations','password_resets','sqlite_sequence']; 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $this->cpAuthorize();

        //Fisrt list all tables
        $tables=[];
        switch(strtolower(env('DB_CONNECTION'))){
            case 'mysql':
                 $tables = DB::select('SHOW TABLES'); // returns an array of 
                 break;
            case 'sqlite':
                 $tables= DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;");
                 break;
            default:
            return view('laradmin::cp.sources', compact('tables'))->with('warning','Unknown database: '.env('DB_CONNECTION'));
        }

        // Remove known system tables from the list
        $temp=[];
        foreach ($tables as $table){
            if(!in_array($table->name,$this->systemTables)){
                $table->label=ucfirst(str_replace('_',' ',$table->name));
                $temp[]=$table;
            }
        } 
        $tables=$temp;
        $pageTitle='Sources';
        return view('laradmin::cp.sources', compact('pageTitle','tables'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->cpAuthorize();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->cpAuthorize();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
     public function show($id)
     {
        $this->cpAuthorize();
     }

    /**
     * Display the specified resource.
     *
     * @param  string  $table
     * @return \Illuminate\Http\Response
     */
    public function showTable($table)
    {
        $this->cpAuthorize();

        $total_rows=count(DB::table($table)->select('id')->get());

        $latest_update= DB::table($table)
        ->latest('updated_at')
        ->first();

        $oldest_update= DB::table($table)
        ->oldest('updated_at')
        ->first();

        $latest_insert= DB::table($table)
        ->latest()
        ->first();

        $oldest_insert= DB::table($table)
        ->oldest()
        ->first();

        //Get permissions
        $source='table:'.$table;
        $permissions=DB::table('main_permissions')->where('source','=',$source)->get();
        $temp=[];
        foreach($permissions as $perm){

            if($perm->user_id){
                $perm->data_id=$perm->user_id;
                $user=DB::table('users')->select(['name','email'])->where('id',$perm->user_id)->first();
                $perm->name=$user->name;
                $perm->isGroup=0;
                $perm->email=$user->email;
                
            }elseif($perm->user_group_id){
                $perm->data_id=$perm->user_group_id;
                $user_group=DB::table('user_groups')->select('name')->where('id',$perm->user_group_id)->first();
                $perm->name=$user_group->name;
                $perm->isGroup=1;
                $perm->email='group';
            }else{
                $perm->name='*Unknown*';
                $perm->isGroup='';
            }
            $temp[]=$perm;
        }
        $permissions=$temp;
        $pageTitle=ucfirst(str_replace('_',' ',$table));
        return view('laradmin::cp.source_table',compact('pageTitle','source','table','total_rows','latest_update','oldest_update','latest_insert','oldest_insert','permissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->cpAuthorize();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->cpAuthorize();
    }


    /**
     * Update permission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     public function updatePermission(Request $request)
     {
        $this->cpAuthorize();
        //dd($request->permission_str);
        if($request->permission_id!=0){
            //$permissions=DB::table('main_permissions')->update($request->permission_id;
            $perm=MainPermission::findOrFail($request->permission_id);
            //var_dump($perm->id);
            //exit();
            $perm_str=str_split($request->permission_str);
            //var_dump($request->permission_str);
            //exit();
            $perm->create=intval( $perm_str[0]);
            $perm->read=intval( $perm_str[1]);
            $perm->update=intval( $perm_str[2]);
            $perm->delete=intval( $perm_str[3]);
            $perm->save();
            $request->flashOnly('permission_id');
            return back()->with('success','Permission Updated');
        }else{/*
            $perm= new MainPermission();
            if($request->user_id){
                $perm->user_id=$request->user_id;
            }elseif($request->user_group_id){
                $perm->user_group_id=$request->user_group_id;
            }else{
                $request->flashOnly('permission_id');
                return back()->with('warning','No group or user specified for the permission');
            }
            $perm_str=str_split($request->permission_str);
            $perm->create=intval( $perm_str[0]);
            $perm->read=intval( $perm_str[0]);
            $perm->update=intval( $perm_str[0]);
            $perm->delete=intval( $perm_str[0]);
            $perm->save();
            $request->flashOnly('permission_id');
            return back()->with('success','Permission added');
            */

            $request->flashOnly('permission_id');
            return back()->with('warning','No group or user specified for the permission');
        }

     }

     /**
     * Find users and groups
     *@param 
     * @param  Request $request
     * @return Illuminate/Http/JsonResponse
     */
    public function searchUsers(Request $request)
    {   
        $this->cpAuthorize();
        //$this->validate($request,['name'=>'required']);
        //abort(422,'data no acess');
        //return response()->json(['error' => 'Error msg'], 404);

        if (Gate::denies('cp')) {
            //abort(403);
            return response()->json(['msg'=>'Access denied'],403);
        }

        if (strlen($request->s)<2){
            return response()->json([]);
        }

        $finds=[];
        $users=DB::table('users')->where('name','like','%'.$request->s.'%')->get();
        foreach($users as $user){
            $temp=[];
            $temp=['id'=>$user->id,'name'=>$user->name,'email'=>$user->email,'isgroup'=>0];
            
            $finds[]=$temp;
        }
        $groups=DB::table('user_groups')->where('name','like','%'.$request->s.'%')->get();

        foreach($groups as $g){
            $temp=[];
            $temp=['id'=>$g->id,'name'=>$g->name,'email'=>'group','isgroup'=>1];
            
            $finds[]=$temp;
        }

        return response()->json($finds);
    }


     /**
     * Store a new permision
     *
     * @param  Request $request
     * @return Illuminate/Http/JsonResponse :with newly created permision id on success
     */
     public function storePermission(Request $request)
     {   
        $this->cpAuthorize();

        if (Gate::denies('cp')) {
            return response()->json(['msg'=>'Access denied'],403);
        }

        if(!$request->data_id){
            //we don't know who to assign the permission to
            return response()->json(['id'=>-1]);
        }
         

        $perm=new MainPermission();
        if($request->isgroup){
             //check permission 
             $permExists=MainPermission::where('source',$request->source)->where('user_group_id',$request->data_id)->first();
             if($permExists){
                return response()->json(['id'=>-1]);
             }
            $perm->user_group_id=$request->data_id;
        }
        else{
            //check permission 
            $permExists=MainPermission::where('source',$request->source)->where('user_id',$request->data_id)->first();
            if($permExists){
                return response()->json(['id'=>-1]);
            }
            $perm->user_id=$request->data_id;
        }
        $perm->source=$request->source;
        $perm->create=0;
        $perm->read=0;
        $perm->update=0;
        $perm->delete=0;

        $perm->save();

        return response()->json( ['id'=>$perm->id]);

     }

     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(MainPermission $permission)
    {
        $this->cpAuthorize();

        $permission->delete();
        return back()->with('success','Permission was deleted');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @return Illuminate/Http/JsonResponse
     */
    public function destroyPermission(Request $request)
    {
        $this->cpAuthorize();
        
        if (Gate::denies('cp')) {
            return response()->json(['msg'=>'Access denied'],403);
        }

        $perm=MainPermission::findOrFail($request->id);
        $perm->delete();
        return response()->json(['id'=>$perm->id]);
    }
}
