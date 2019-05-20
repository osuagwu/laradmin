<?php

namespace  BethelChika\Laradmin\Http\Controllers\CP;

use Illuminate\Http\Request;

use DB;
use BethelChika\Laradmin\MainPermission;
use BethelChika\Laradmin\Source;
use Illuminate\Support\Facades\Gate;
use BethelChika\Laradmin\Http\Controllers\CP\Controller; //NOTE: This is explicitly imported to avoid wrong use of a controller if this file is coppied elsewhere
use Illuminate\Support\Facades\Auth;
use BethelChika\Laradmin\WP\Models\Page;

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


   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->cpAuthorize();
    }
  
  
   /** Display a listing of pages
    *
    * @return \Illuminate\Http\Response
    */
    public function pages()
    {
        
        $this->cpAuthorize();
       
        $pages=Page::where('post_type','page')->paginate(5);
         
        $pageTitle='Pages';
        return view('laradmin::cp.source.pages', compact('pageTitle','pages'));
    }
       
    
    /**
     * Display a page source
     *
     * @return \Illuminate\Http\Response
     */
    public function showPage($id)
    {
       $this->cpAuthorize();
       $page=Page::where('id',$id)->first();
       
        if(!$page){
            return redirect()->route('cp-source-type-page')->with('warning','Unknown page. id:'.$id);
        }
       $source_name=$page->ID;//Note The page ID is used as a name here for main_persion table, just works as a unique id for pages type if source
       $source_type=Source::getPageTypeKey($page);
       $pageTitle=$page->title;
       
       return view('laradmin::cp.source.show_page', compact('pageTitle','source_type','source_name','page'));
    }

      /**
     * Display types that do not have their own routes and are stored in the sources table.
     *
     * @param  int $type type of Source
     * @return \Illuminate\Http\Response
     */
    public function type($type)
    {
       $this->cpAuthorize();
       $source_types=Source::$DEFAULT_TYPES;
       $sources=Source::where('type',strtolower($type))->paginate(5);
       $pageTitle=ucfirst($type);
        return view('laradmin::cp.source.type', compact('pageTitle','source_types','sources'));
    }

   
    /**
     * Display types of sources.
     *
     * @return \Illuminate\Http\Response
     */
    public function types()
    {
       $this->cpAuthorize();
       $source_types=Source::$DEFAULT_TYPES;
        $pageTitle='Sources types';
        return view('laradmin::cp.source.types', compact('pageTitle','source_types'));
    }



    /**
     * Display the specified resource.
     *
     * @param  string $type
     * @param string $id
     * @return \Illuminate\Http\Response
     */
     public function show($id)
     {
        $this->cpAuthorize();
        $source=Source::find($id);
        if(!$source){
            return redirect()->route('cp-source-types')->with('warning','Unknown page. id:'.$id);
        }
       $source_name=$source->id;
       $source_type=Source::getTypeKey($source);
        $pageTitle=ucfirst($source->type) .' : '.$source->name;
        return view('laradmin::cp.source.show', compact('pageTitle','source_type','source_name'));
     }

     
    /** Display a listing of route
    *
    * @return \Illuminate\Http\Response
    */
   public function routes()
   {
       
       $this->cpAuthorize();
       $routes = app('router')->getRoutes();
    //    foreach($routes as $route){
    //     //dd($route->getActionMethod());
    //     //dd($route->prefix());
    //        dd($route->methods());
    //    }
       
        
       $pageTitle='Routes';
       return view('laradmin::cp.source.routes', compact('pageTitle','routes'));
   }

   /**
     * Display a Route source
     *
     * @return \Illuminate\Http\Response
     */
    public function showRoute(Request $request)
    {
       $this->cpAuthorize();
       
       $route_name='';
       if($request->filled('name')){
           $route_name=$request->input('name');
       }

       $methods=$request->input('methods');
        $action=$request->input('action');
        $uri=$request->input('uri');

       $route=null;
       $routes = app('router')->getRoutes();
        foreach($routes as $_route){
        
                $_methods=implode('|',$_route->methods());
                $_action=$_route->getActionName();
                $_route_name=$_route->getName();
                $_uri=$_route->uri();

                $is_route_name=true;
                if($route_name){
                    $is_route_name=str_is($route_name,$_route_name);
                }

                if(str_is($methods,$_methods)
                    and str_is($action,$_action)
                    and str_is($uri,$_uri)
                    and $is_route_name){
                        $route=$_route;
                        break;
                    }
        }
        if(!$route){
            return redirect()->route('cp-source-type-route')->with('warning','Unknown route. uri:'.$uri);
        }
       $source_name=$route->uri();
       $source_type=Source::getRouteTypeKey($route);
       $pageTitle=$route->uri();
       
       return view('laradmin::cp.source.show_route', compact('pageTitle','source_type','source_name','route'));
    }


       /** Display a listing of route
    *
    * @return \Illuminate\Http\Response
    */
   public function routePrefixes()
   {
       
       $this->cpAuthorize();
       $routes = app('router')->getRoutes();
       $route_=[];
       foreach($routes as $route){
            if($route->getPrefix()){
                $route_[]=$route;
            }
       }
       $routes=$route_;
       unset($route_);
       $pageTitle='Routes with prefixes';
       return view('laradmin::cp.source.routes', compact('pageTitle','routes'));
   }

    /**
     * Display a Route source
     *
     * @return \Illuminate\Http\Response
     */
    public function showRoutePrefix(Request $request)
    {
       $this->cpAuthorize();
       
       $route_name='';
       if($request->filled('name')){
           $route_name=$request->input('name');
       }

       $methods=$request->input('methods');
        $action=$request->input('action');
        $uri=$request->input('uri');
        $prefix=$request->input('prefix');

       $route=null;
       $routes = app('router')->getRoutes();
        foreach($routes as $_route){
            $_methods=implode('|',$_route->methods());
            $_action=$_route->getActionName();
            $_route_name=$_route->getName();
            $_uri=$_route->uri();
            $_prefix=$_route->getPrefix();

            $is_route_name=true;
            if($route_name){
                $is_route_name=str_is($route_name,$_route_name);
            }

            if(str_is($methods,$_methods)
                and str_is($action,$_action)
                and str_is($uri,$_uri)
                and str_is($prefix,$_prefix)
                and $is_route_name){
                    $route=$_route;
                    break;
                }
        }

        if(!$route){
            return redirect()->route('cp-source-type-route')->with('warning','Unknown route prefix. route prefix:'.$prefix);
        }
       $source_name=$route->getPrefix();
       $source_type=Source::getRoutePrefixTypeKey($route);
       $pageTitle=$route->uri();
       
       return view('laradmin::cp.source.show_route', compact('pageTitle','source_type','source_name','route'));
    }



      /**
     * Display a listing of tables
     *
     * @return \Illuminate\Http\Response
     */
    public function tables()
    {
        
        $this->cpAuthorize();

        //First get all table
        $tables=Source::getAllTables();
        
        
        $pageTitle='Tables';
        return view('laradmin::cp.source.tables', compact('pageTitle','tables'));
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function showTable(Request $request, $name)
    {
        $this->cpAuthorize();
        //dd($request->user()->getConnection());
        $table=$name;
        $connection=$request->input('connection');
        $database=$request->input('database');
        $prefix=$request->input('prefix');


        foreach(Source::getAllTables() as $_table){
            if(str_is($_table->name,$table)
                and str_is($_table->connection,$connection)
                and str_is($_table->connection_info['database'],$database)
                and str_is($_table->connection_info['prefix'],$prefix)
                ){
                    $table=$_table;
                    break;
                }
        }


        if(!$table){
            return redirect()->route('cp-source-type-table')->with('warning','No such table: '.$table);
        }

        $total_rows=count(DB::table($table->name)->select('id')->get());

        $latest_update= DB::table($table->name)
        ->latest('updated_at')
        ->first();

        $oldest_update= DB::table($table->name)
        ->oldest('updated_at')
        ->first();

        $latest_insert= DB::table($table->name)
        ->latest()
        ->first();

        $oldest_insert= DB::table($table->name)
        ->oldest()
        ->first();

        $source_name=$table->name;
        $source_type=Source::getTableTypeKey($connection,$database,$prefix,$table->name);
        $pageTitle=ucfirst(str_replace('_',' ',$table->name));
        return view('laradmin::cp.source.show_table',compact('pageTitle','source_name','source_type','table','total_rows','latest_update','oldest_update','latest_insert','oldest_insert','permissions'));
    }

     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->cpAuthorize();
        $source_types=Source::$UNGUARDED_DEFAULT_TYPES;


        $pageTitle='Create a source';
        return view('laradmin::cp.source.create', compact('pageTitle','source_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->cpAuthorize();//dd(in_array($request->input('type'),['File','the']));
        if(!in_array($request->input('type'),array_keys(Source::$UNGUARDED_DEFAULT_TYPES))){
            return redirect()->back()->withInput($request->input())->with('danger','You cannot create the type: '. $request->input('type'));
        }

        $source=new Source;
       
        $source->name=$request->name;
        
        $source->description=$request->description;
        $source->type=$request->input('type');
        $source->user_id=Auth::user()->id;

        if(str_is($source->type,'url')){
            $source->name=trim($source->name,'/');
        }

        $source->save();
        return redirect()->route('cp-source-type',[$source->type])->with('success','Done');

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
        //dd($finds);

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
