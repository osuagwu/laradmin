<?php

namespace  BethelChika\Laradmin\Http\Controllers\CP;

use Illuminate\Http\Request;
use BethelChika\Laradmin\User;
use BethelChika\Laradmin\UserGroup;
use Illuminate\Support\Facades\Gate;
use BethelChika\Laradmin\Permission;
use BethelChika\Laradmin\Http\Controllers\CP\Controller; //NOTE: This is explicitly imported to avoid wrong use of a controller if this file is coppied elsewhere

class PermissionController extends Controller
{   /*
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->cpAuthorize();
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->cpAuthorize();
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
     * Update permission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
       $this->cpAuthorize();
       //dd($request->permission_str);
       if($request->permission_id!=0){
           //$permissions=DB::table('permissions')->update($request->permission_id;
           $perm=Permission::findOrFail($request->permission_id);
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
           $perm= new Permission();
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
       $users=User::where('name','like','%'.$request->s.'%')->get();
       foreach($users as $user){
           $temp=[];
           $temp=['id'=>$user->id,'name'=>$user->name,'email'=>$user->email,'isgroup'=>0];
           
           $finds[]=$temp;
       }
       $groups=UserGroup::where('name','like','%'.$request->s.'%')->get();

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
    public function store(Request $request)
    {   
       $this->cpAuthorize();

       if (Gate::denies('cp')) {
           return response()->json(['msg'=>'Access denied'],403);
       }

       if(!$request->data_id){
           //we don't know who to assign the permission to
           return response()->json(['id'=>-1]);
       }
        

       $perm=new Permission();
       if($request->isgroup){
            //check permission 
            $permExists=Permission::where('source_type',$request->source_type)
            ->where('source_id',$request->source_id)
            ->where('user_group_id',$request->data_id)->first();
            if($permExists){
               return response()->json(['id'=>-1]);
            }
           $perm->user_group_id=$request->data_id;
       }
       else{
           //check permission 
           $permExists=Permission::where('source_type',$request->source_type)
           ->where('source_id',$request->source_id)
           ->where('user_id',$request->data_id)->first();
           if($permExists){
               return response()->json(['id'=>-1]);
           }
           $perm->user_id=$request->data_id;
       }
       $perm->source_type=$request->source_type;
       $perm->source_id=$request->source_id;
       $perm->create=0;
       $perm->read=0;
       $perm->update=0;
       $perm->delete=0;

       $perm->save();

       return response()->json( ['id'=>$perm->id]);

    }

//     /**
//     * Remove the specified resource from storage.
//     *
//     * @param  int  $id
//     * @return \Illuminate\Http\Response
//     */
//    public function destroy(Permission $permission)
//    {
//        $this->cpAuthorize();

//        $permission->delete();
//        return back()->with('success','Permission was deleted');
//    }
 
   /**
    * Remove the specified resource from storage.
    *
    * @param  Request $request
    * @return Illuminate/Http/JsonResponse
    */
   public function destroy(Request $request)
   {
       $this->cpAuthorize();
       
       if (Gate::denies('cp')) {
           return response()->json(['msg'=>'Access denied'],403);
       }

       $perm=Permission::findOrFail($request->id);
       $perm->delete();
       return response()->json(['id'=>$perm->id]);
   }
}
