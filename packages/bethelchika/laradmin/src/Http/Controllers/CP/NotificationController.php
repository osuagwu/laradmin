<?php

namespace BethelChika\Laradmin\Http\Controllers\CP;

use BethelChika\Laradmin\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use BethelChika\Laradmin\Notifications\Notice;
use BethelChika\Laradmin\Http\Controllers\CP\Controller; //NOTE: This is explicitly imported to avoid wrong use of a controller if this file is coppied elsewhere

class NotificationController extends Controller
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

        //$this->authorize('view', Auth::user()); // 
        $cp=(new User)->getSystemUser();

        $notices=$cp->notifications()->where('type','=',Notice::class)->paginate(5);
        //dd(Notice::class);
        return view('laradmin::cp.notifications.index',['notices'=>$notices]);
     
     
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
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->cpAuthorize();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string $notice Id 
     * @return \Illuminate\Http\Response
     */
    public function destroy($notification)
    {
        $this->cpAuthorize();

        //$this->authorize('delete', Auth::user()); // anyone who can delete the user is here allowed to delete the users notifications
        (new User)->getSystemUser()->notifications()->where('id','=',$notification)->delete();
        return back();//->with('success','Notification was deleted');
    }
    /**
     * Mark specified notifications as read/unraed.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsAjax(Request $request){
        $this->cpAuthorize();
        
       $cp= (new User)->getSystemUser();

        //$this->authorize('update', Auth::user()); // anyone who can update the user is here allowed to update the users notifications
        $notices=$request->notices;
        //dd($notices);
        $notices=$cp->notifications()->whereIn('id',$notices)->get();

        if(count($notices)==0){
            return response()->json([]);
        }
        
        if(!strcmp($request->mark_as,'read')){
            $notices->markAsRead();
        }else{
            foreach($notices as $notice){
                $notice->update(['read_at' => null]);
            }

            //I am not sure here if it is neccessary to read the notifications again from the DB, but I will just do it anyways
            $notices=$request->notices;
            $notices=$cp->notifications()->whereIn('id',$notices)->get();
        }

        $n2=[];
        foreach($notices as $n){
            $temp['id']=$n->id;
            $temp['read_at']=$n->read_at?$n->read_at:0;

            $n2[]=$temp;
            $temp=[];
        }
        return response()->json($n2);
        
    }
}
