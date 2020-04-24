<?php

namespace  BethelChika\Laradmin\Http\Controllers\CP;

use BethelChika\Laradmin\UserGroup;
use BethelChika\Laradmin\UserGroupMap;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use BethelChika\Laradmin\Http\Controllers\CP\Controller; //NOTE: This is explicitly imported to avoid wrong use of a controller if this file is coppied elsewhere
class UserGroupController extends Controller
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
        //$user = Auth::user();
        $this->authorize('views', 'BethelChika\Laradmin\UserGroup');

        $request=Request();

        $order_by=$request->get('order_by','id');
        $order_by_dir=$request->get('order_by_dir','asc');
        $currentOrder=$order_by.':'.$order_by_dir;

        
        
        
        if($request->search){
            $search_str='%'.$request->get('user_groups_search').'%';
            $userGroups=UserGroup::where('name','like',$search_str)->orderBy($order_by,$order_by_dir)->paginate(10);
            $request->flash('user_groups_search');
        }
        else{
                
            $userGroups=UserGroup::orderBy($order_by,$order_by_dir)->paginate(10);
        }
        return view('laradmin::cp.user_groups',['userGroups'=>$userGroups,'currentOrder'=>$currentOrder]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->cpAuthorize();
        $this->authorize('create', 'BethelChika\Laradmin\UserGroup');
        return view('laradmin::cp.user_group_create');
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

        $this->authorize('create', 'BethelChika\Laradmin\UserGroup');

        $this->validate($request, [
            'name' => 'required|unique:user_groups|string|max:255',
            'description' => 'required|string|max:255',
          ]);

        $userGroup=new UserGroup;
        $userGroup->name=$request->name;
        $userGroup->description=$request->description;
        $userGroup->save();

        return redirect()->route('cp-user-groups')->with('success', 'User group sucessfully created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UserGroup  $userGroup
     * @return \Illuminate\Http\Response
     */
    public function show(UserGroup $userGroup)
    {
        $this->cpAuthorize();

        $this->authorize('view', $userGroup);

        return view('laradmin::cp.user_group',['userGroup'=>$userGroup]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UserGroup  $userGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(UserGroup $userGroup)
    {
        $this->cpAuthorize();

        $this->authorize('update', $userGroup);

        return view('laradmin::cp.user_group_edit',compact('userGroup'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserGroup  $userGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserGroup $userGroup)
    {
        $this->cpAuthorize();

        $this->authorize('update', $userGroup);
        
        $this->validate($request, [
            'name' => [
                'required',
                Rule::unique('user_groups')->ignore($userGroup->id),
            ],
            'description' => [
                'required',
                'string',
                'max:255',
            ]
        ]);

        
        $userGroup->name=$request->name;
        $userGroup->description=$request->description;
        $userGroup->save();

        return redirect()->route('cp-user-groups')->with('success', 'User group sucessfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserGroup  $userGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserGroup $userGroup)
    {
        $this->cpAuthorize();

        $this->authorize('delete', $userGroup);

        foreach ($userGroup->userGroupMap as $userGroupMap){
            $userGroupMap->delete();
        }
        $userGroup->delete();
        return back()->with('success', 'User group sucessfully deleted');
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     public function destroys(Request $request)
     {
        $this->cpAuthorize();
        
         
         $i=0;
         foreach (explode(',',$request->user_groups_ids) as $user_groups_id){
            $userGroup=UserGroup::find($user_groups_id);

            $this->authorize('delete', $userGroup);

            //If the user is authorised to delete the group then the user should be allowed to delete the user_group mappings
            if(!$userGroup)continue;
            foreach ($userGroup->userGroupMap as $userGroupMap){
                $userGroupMap->delete();//NOTE that databse level constrain may have taken care of this.
            }
            $i++;
            $userGroup->delete();
        }
         if($i)
         return back()->with('success', $i.' user group(s) sucessfully deleted');
         else 
         return back()->with('info', 'Nothing was deleted');
     }
}
