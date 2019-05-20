<?php

namespace  BethelChika\Laradmin\Http\Controllers\CP;

use BethelChika\Laradmin\UserGroupMap;
use Illuminate\Http\Request;
use BethelChika\Laradmin\User;
use BethelChika\Laradmin\UserGroup;
use BethelChika\Laradmin\Http\Controllers\CP\Controller; //NOTE: This is explicitly imported to avoid wrong use of a controller if this file is coppied elsewhere
class UserGroupMapController extends Controller
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->cpAuthorize();
        //
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
     * @param  \App\UserGroupMap  $userGroupMap
     * @return \Illuminate\Http\Response
     */
    public function show(UserGroupMap $userGroupMap)
    {
        $this->cpAuthorize();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UserGroupMap  $userGroupMap
     * @return \Illuminate\Http\Response
     */
    public function edit(UserGroupMap $userGroupMap)
    {
        $this->cpAuthorize();
    }

    /**
     * Show the form for editing the resources assigned to the specified user.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    function edits(User $user){
        $this->cpAuthorize();

        $this->authorize('updates',['BethelChika\Laradmin\UserGroupMap',$user]);
        
        $user_groups_mapped=[];
        $mapped_ids=[];
        foreach ($user->userGroupMap as $ugm){
           
            $user_groups_mapped[]=UserGroup::find($ugm->user_group_id);
            $mapped_ids[]=($ugm->user_group_id);
            //var_dump($ugm->user_group_id);
        }

        $user_groups=UserGroup::get();
        $user_groups_unmapped=[];
        foreach($user_groups as $user_group){
            if(!in_array($user_group->id,$mapped_ids)){
                $user_groups_unmapped[]=$user_group;
                
            }
            //print $user_group->id.'=='.implode(',',$mapped_ids).'</br>';
        }
     
                
        return view('laradmin::cp.user_group_map_edits',compact('user','user_groups_unmapped','user_groups_mapped'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserGroupMap  $userGroupMap
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserGroupMap $userGroupMap)
    {
        $this->cpAuthorize();
        
        $this->authorize('update', $userGroupMap);

    }


    /**
     * Update the user group mappings for the specified user based on request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    function updates(Request $request,User $user){
        $this->cpAuthorize();
        

        $this->authorize('updates',['BethelChika\Laradmin\UserGroupMap',$user]);

        $this->validate($request, [
            'member_of' => 'nullable|string|max:255',
          ]);

        //exit(var_dump(strcmp(strtolower($user->name),'super user')));
        //if(!strcmp(strtolower($user->name),'super')){//TODO: find better way to do this
        //    return redirect()->route('cp-user',$user->id)->with('warning', 'Cannot alter the group membership of this user!');
            
        //}
        

        $new_user_maps=[];
        if($request->member_of){
            $new_user_maps=explode(',',$request->member_of);
            array_walk($new_user_maps,function(&$val,$key){$val=intval($val);});
        }

        $user_maps=$user->userGroupMap;
        $changes=0;
        foreach($user_maps as $user_map){
            //var_dump($user_map);
            //exit();
            if(in_array(intval($user_map->user_group_id),$new_user_maps,true)){
                //this is the db already so don't add it again
                $key_found=array_search(intval($user_map->user_group_id),$new_user_maps);
                array_splice($new_user_maps,$key_found,1);
            }else{
                //The map is in the db but not in the new list of maps. so remove it from db.
                $user_map->delete();
                $changes++;
            }
        }
        
        //now add any remaning new maps to db
        foreach($new_user_maps as $new_user_map){ 
            $ugm=new UserGroupMap();
            $ugm->user_id=$user->id;
            $ugm->user_group_id=$new_user_map;
            $ugm->save();
            $changes++;
        }
        if($changes)return redirect()->route('cp-user',$user->id)->with('success', 'User group membership updated!');
        else return redirect()->route('cp-user',$user->id)->with('info', 'No changes were made to the group membership!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserGroupMap  $userGroupMap
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserGroupMap $userGroupMap)
    {
        $this->cpAuthorize();
        
        $this->authorize('delete', $userGroupMap);
    }
}
