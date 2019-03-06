<?php

namespace BethelChika\Laradmin\Http\Controllers\User;

use Illuminate\Http\Request;
use BethelChika\Laradmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use BethelChika\Laradmin\Notifications\Notice;

class NotificationController extends Controller
{
    /**
     * TODO:
     * # limit the number of notices per user, perhapse delete old notices
     */
    /**
     * Create a new controller instance.
     *
     * @return void
     */
     public function __construct()
     {
        $this->middleware('auth');
     }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', Auth::user()); // anyone who can read the user is here allowed to read the users notifications

        $notices=Auth::user()->notifications()->where('type','=',Notice::class)->paginate(5);
        //dd(Notice::class);
        $pageTitle='Notifications';
        return view('laradmin::user.notifications.index',['notices'=>$notices,'pageTitle'=>$pageTitle]);
     
     
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(403);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort(403);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort(403);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort(403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        abort(403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string $notice Id 
     * @return \Illuminate\Http\Response
     */
    public function destroy($notification)
    {
        $this->authorize('delete', Auth::user()); // anyone who can delete the user is here allowed to delete the users notifications
        Auth::user()->notifications()->where('id','=',$notification)->delete();
        return back();//->with('success','Notification was deleted');
    }
    /**
     * Mark specified notifications as read/unraed.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsAjax(Request $request){
        $this->authorize('update', Auth::user()); // anyone who can update the user is here allowed to update the users notifications
        $notices=$request->notices;
        //dd($notices);
        $notices=Auth::user()->notifications()->whereIn('id',$notices)->get();

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
            $notices=Auth::user()->notifications()->whereIn('id',$notices)->get();
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
