<?php

namespace  BethelChika\Laradmin\Http\Controllers\CP;

use Illuminate\Http\Request;

use DB;
use BethelChika\Laradmin\Permission;
use BethelChika\Laradmin\Source;
use Illuminate\Support\Facades\Gate;
use BethelChika\Laradmin\Http\Controllers\CP\Controller; //NOTE: This is explicitly imported to avoid wrong use of a controller if this file is coppied elsewhere
use Illuminate\Support\Facades\Auth;
use BethelChika\Laradmin\WP\Models\Page;

class SourceController extends Controller
{/*
    TODO: No policy is applied yet in this controller so their is no authorisation for corresponding table and model
    */
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
  
      /**
     * Display types of sources.
     *
     * @return \Illuminate\Http\Response
     */
    public function types()
    {
       $this->cpAuthorize();
       $source_types=Source::$DEFAULT_TYPES;
        $pageTitle='Source types';
        return view('laradmin::cp.source.types', compact('pageTitle','source_types'));
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
       $source_id=$page->ID;//Note The page ID is used as a name here for main_persion table, just works as a unique id for pages type if source
       $source_type=get_class($page);
       $pageTitle=$page->title;
       
       return view('laradmin::cp.source.show_page', compact('pageTitle','source_type','source_id','page'));
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
     * Display the specified resource.
     *
     * @param  string $type
     * @param string $id
     * @return \Illuminate\Http\Response
     */
     public function show($type,$id)
     {
        $this->cpAuthorize();
        $source=Source::find($id);
        if(!$source){
            return redirect()->route('cp-source-types')->with('warning','Unknown page. id:'.$id);
        }
       $source_id=$source->id;
       $source_type=Source::class;
        $pageTitle=ucfirst($source->type) .' : '.$source->name;
        return view('laradmin::cp.source.show', compact('pageTitle','source_type','source_id'));
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
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function showRoute(Request $request)
    {
       $this->cpAuthorize();
       
       $route_name='';
       if($request->filled('name')){
           $route_name=$request->input('name');
       }


       $prefix='';
       if($request->filled('prefix')){
           $prefix=$request->input('prefix');
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
                $_prefix=$_route->getPrefix();
                $_uri=$_route->uri();

                $is_route_name=true;
                if($route_name){
                    $is_route_name=str_is($route_name,$_route_name);
                }

                $is_prefix=true;
                if($prefix){
                    $is_prefix=str_is($prefix,$_prefix);
                }

                if(str_is($methods,$_methods)
                    and str_is($action,$_action)
                    and str_is($uri,$_uri)
                    and $is_route_name
                    and $is_prefix){
                        $route=$_route;
                        break;
                    }
        }
        if(!$route){
            return redirect()->route('cp-source-type-route')->with('warning','Unknown route. uri:'.$uri);
        }
       $source_type='route';
       $source_id=Source::getRouteSourceId($route);
       $pageTitle=$route->uri();
       
       return view('laradmin::cp.source.show_route', compact('pageTitle','source_type','source_id','route'));
    }


       /** Display a listing of routee with prefix
    *
    * @return \Illuminate\Http\Response
    */
   public function routePrefixes()
   {
       
       $this->cpAuthorize();
       $routes = app('router')->getRoutes();
       $prefixes=[];
       foreach($routes as $route){
            if($route->getPrefix()){
                $prefixes[]=$route->getPrefix();
            }
            
       }
       $prefixes=array_unique($prefixes);
       //dd($routes);
       unset($route_);
       $pageTitle='Routes prefixes';
       return view('laradmin::cp.source.route_prefixes', compact('pageTitle','prefixes'));
   }

    /**
     * Display a Route source with prefix
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function showRoutePrefix(Request $request)
    {
       $this->cpAuthorize();
       
       $name=$request->input('name');

       $prefix=null;
       $routes = app('router')->getRoutes();
        foreach($routes as $_route){
            $_prefix=$_route->getPrefix();

            if(str_is($name,$_prefix)){
                $prefix=$_prefix;
                break;
            }
        }

        if(!$prefix){
            return redirect()->route('cp-source-type-route_prefix')->with('warning','Unknown route prefix. route prefix:'.$prefix);
        }
       $source_id=$prefix;
       $source_type='route_prefix';
       $pageTitle=$name;
       
       return view('laradmin::cp.source.show_route_prefix', compact('pageTitle','source_type','source_id','prefix'));
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
     * @param Request $request
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
        
        $total_rows=null;
        $latest_update=null;
        $oldest_update=null;
        $latest_insert=null;
        $oldest_insert=null;
        try{//We try b/c not all tables will have all the fields we are asking for also see todo below about worpress not working

            //TODO: wordpress table with prefix is not working as Laravel is appending the prefix twice
            //DB::connection($connection)->setTablePrefix('');
            //dd(DB::connection($connection)->table($table->name)->get());
            $total_rows=count(DB::connection($connection)->table($table->name)->select('*')->get());

            $latest_update= DB::connection($connection)->table($table->name)
            ->latest('updated_at')
            ->first();

            $oldest_update= DB::connection($connection)->table($table->name)
            ->oldest('updated_at')
            ->first();

            $latest_insert= DB::connection($connection)->table($table->name)
            ->latest()
            ->first();

            $oldest_insert= DB::connection($connection)->table($table->name)
            ->oldest()
            ->first();
        }catch(\Illuminate\Database\QueryException $qex){
        }

        $source_type='table';
        $source_id=Source::getTableSourceId($connection,$database,$prefix,$table->name);
        $pageTitle=ucfirst(str_replace('_',' ',$table->name));
        return view('laradmin::cp.source.show_table',compact('pageTitle','source_id','source_type','table','total_rows','latest_update','oldest_update','latest_insert','oldest_insert','permissions'));
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

        if(str_is($source->type,'model')){
            $source->name=trim($source->name,'\\');
        }

        $source->save();
        return redirect()->route('cp-source-type',[$source->type])->with('success','Done');

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  Source $source
     * @return \Illuminate\Http\Response
     */
    public function edit($type,Source $source)
    {
        $this->cpAuthorize();
        $source_types=Source::$UNGUARDED_DEFAULT_TYPES;
        $pageTitle='Edit '.$source->name;
        return view('laradmin::cp.source.edit',compact('pageTitle','source','source_types'));
    }

    /**
     * Update the specified resource in storage.
     *
    * @param  Source $source
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$type,Source $source)
    {
        $this->cpAuthorize();
        $source->name=$request->input('name');
        $source->type=$request->input('type');
        $source->description=$request->input('description');

        if(str_is($source->type,'url')){
            $source->name=trim($source->name,'/');
        }

        if(str_is($source->type,'model')){
            $source->name=trim($source->name,'\\');
        }

        if($source->save()){
            return redirect()->route('cp-source-show',[$source->type,$source->id])->with('success','Done!');
        }else{
            return back()->withInput($request->all())->with('danger','Error with saving item');
        }
        
    }

    /**
     * Delete a source
     *
     * @param Source $source
     * @return \Illuminate\Http\Response
     */
    public function destroy(Source $source){
        $this->cpAuthorize();
        $type_key=Source::getTypeKey();
        if(Permission::unlinkSource($type_key,$source->id)){
            $type=$source->type;
            $source->delete();
            return redirect()->route('cp-source-type',$type)->with('success','Done!');
        }else{
            return back()->with('danger','Unable to complete the action');
        }
        
    }

    
}
