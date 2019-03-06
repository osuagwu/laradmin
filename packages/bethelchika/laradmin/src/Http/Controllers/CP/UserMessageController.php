<?php

namespace BethelChika\Laradmin\Http\Controllers\CP;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use BethelChika\Laradmin\User;
use Illuminate\Http\Request;
use BethelChika\Laradmin\UserMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use BethelChika\Laradmin\Permission\Permission;
use BethelChika\Laradmin\Mail\UserMessageMail;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use BethelChika\Laradmin\Http\Controllers\CP\Controller; //NOTE: This is explicitly imported to avoid wrong use of a controller if this file is coppied elsewhere

class UserMessageController extends Controller
{
    
    private $cpId=null;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
     public function __construct()
     {
         $this->middleware('auth');
         parent::__construct();
         $this->cpId=(new User)->getCPId();
     }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->cpAuthorize();
        
        $this->authorize('cpViews','BethelChika\Laradmin\UserMessage');

        $user=User::findOrFail($this->cpId);

        $request=request();
        $order_by=$request->get('order_by','created_at');
        $order_by_dir=$request->get('order_by_dir','desc');
        $currentOrder=$order_by.':'.$order_by_dir;

        //---------------------------------------------------
        $search_str=false;
        if($request->search){
            $search_str='%'.$request->get('user_messages_search').'%';
            $request->flash('user_messages_search');
        }

        

        //\Illuminate\Support\Facades\DB::enableQueryLog();
         
        $messages=UserMessage::where(function($query) use ($user,$search_str){
                    $query->where(['user_id'=>$user->id,'deleted_by_receiver_at'=>null])
                    ->where(['parent_id'=>null])
                    ->where(function($query) use($search_str){
                        if($search_str){
                            $query->where('subject','like',$search_str)
                            ->orWhere('message','like',$search_str); 
                        }
                    });
            })
            ->orwhere(function($query)use ($user,$search_str){
                    $query->where('creator_user_id',$user->id)
                    ->where('deleted_by_sender_at',null)
                    ->where(['parent_id'=>null])
                    ->where(function($query) use($search_str){
                        if($search_str){
                            $query->where('subject','like',$search_str)
                            ->orWhere('message','like',$search_str); 
                        }
                    });
            })
            ->orderBy($order_by,$order_by_dir)
            ->paginate(10);//TODO: do not select messages sent only to email, might need to move to mysql in other to use  json

            //dd(\Illuminate\Support\Facades\DB::getQueryLog());



            
        $converses=[];
        foreach($messages as $message){
            $temp=[];

            /* Keep count of the number of unread messages
            */
            $c=0;//$message->read_at?0:1;
            $c+=UserMessage::where(function($query) use ($user,$message){
                            $query->where(['user_id'=>$user->id,'deleted_by_receiver_at'=>null])
                            ->where('read_at',null)
                            ->where('parent_id',$message->id);
            })->orWhere(function($query)use ($user,$message){
                            $query->where(['user_id'=>$user->id,'id'=>$message->id,'read_at'=>null]);
            })->count();
            /*
             ->where(function($query) use ($user,$message){
                            $query->orWhere(['creator_user_id'=>$user->id,'deleted_by_sender_at'=>null])
                            ->where('parent_id',$message->id)
                            ->where('read_at',null);
            })->count(); */

            $temp['unread_count']=$c;
            $temp['message']=$message;
            try{
                $sender=User::findOrFail($message->creator_user_id);
                $temp['sender_name']=$sender->name;
            }catch(ModelNotFoundException $e){
                $temp['sender_name']='--';//We can also assum its guest if we can't find the user, but it could be a deleted user
            }
            $converses[]=$temp;
        }
        //dd($temp);
        return view('laradmin::cp.message.index',compact('messages','converses','currentOrder'));


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->cpAuthorize();
        
        $this->authorize('cpCreate','BethelChika\Laradmin\UserMessage');

        $parent_id='';
        $request=request();
        if($request->parent_id){
            $parent_id=$request->parent_id;
        }
        $userTo='';
        if($request->has('user')){
            $userTo=$request->user;
            try{//dd($userTo);
                $userTo=User::findOrFail($userTo);
                
            }catch(ModelNotFoundException $ex){;
                $userTo=false;
            }
        }

        $returnToUrl=url()->previous();
        //dd($returnToUrl);
        
        $showChannels=true;
        return view('laradmin::cp.message.create',compact('parent_id','userTo','showChannels','returnToUrl'));
    }
    /**
     * Store reply of a message.
     *
     * @return \Illuminate\Http\Response
     */
    public function reply(Request $request)
    {   
        $this->cpAuthorize();
        
        //NOTE: In theis function $user refers to sender:


        $parentMessage=UserMessage::findOrFail($request->parent_id);

        $this->authorize('cpUpdate',$parentMessage);

        $this->validate($request, [
            'message' => 'required|string|max:10000',
            'subject' => 'required|string|max:140',
            //'parent_id'=>'exists:user_messages'
        ]);

        //check that message can be replied to
        if($parentMessage->do_not_reply){
            return back()->with('warning','This message cannot be replied');
        }



        $user=User::findOrFail($this->cpId);//Auth::user();
        $admin=Auth::user();
        
        
        
        
        

        

        //dd($request);
        $userMessage=new UserMessage;
        $userMessage->admin_creator_user_id=$admin->id;
        $userMessage->creator_user_id=$user->id;
        $userMessage->id = Uuid::uuid4()->toString();
        $userMessage->parent_id=$parentMessage->id;
        $userMessage->channels=$parentMessage->channels;
        $userMessage->secret=str_random(40);
        $userMessage->message=$request->message;
        $userMessage->subject=$request->subject;
       
        /*  The person trying to reply has to be the login user. 
            The person to be replied to is either the sender or the 
            user(receiver) of parent message. so send this message to
            either the parent sender or receiver that is not the Auth::user()
        */
        $parentSender=$parentMessage->sender;
        if(!$parentSender->is($user)){
            $receiver=$parentMessage->sender;
        }else{
            $receiver=$parentMessage->user;
        }


        if(is_array($parentMessage->channels)){
            $channels=$parentMessage->channels;
        }else{
            $channels=explode(',',$parentMessage->channels);
        }

        $emailChennelWorked=false;
        if(in_array('email',$channels)){
            
            Mail::to($receiver->email)
            ->send(new UserMessageMail($user,$receiver,$userMessage,$admin));
            $emailChennelWorked=true;
        }

        if(in_array('database',$channels)){

            // Check for quotas
            if(!($userMessage->isWithinAdminQuota($user))){
                $request->flash();
                if($emailChennelWorked){
                    return back()->with('warning','Message was sent via email only because SYSTEM USER have reached your message quota limit');
                }else{
                    return back()->with('danger','Message could not be sent because SYSTEM USER have reached your message quota limit');
                }
            }

            if(!($userMessage->isWithinUserQuota($receiver))){
                $request->flash();
                if($emailChennelWorked){
                    return back()->with('warning','Message was sent via email only because your receiver has reached a set message quota limit');
                }else{
                    return back()->with('danger','Message could not be sent because your receiver has reached a set message quota limit');
                }
            }

            //save quotas
            $userMessage->addToQuota($receiver);
            if(!($receiver->is($user))){
                $userMessage->addToQuota($user);
            }

            //send reply
            $receiver->userMessages()->save($userMessage); 
        }



        //********************************************************************* */
        /* FIXME:: If a user deletes a parent message, he will not see subsequent replies
        *   This will need to be fixed better in index function retrival of parents messages
        *   But we will do a quick lazy temporary fix here
        *///$parentMessage->update(['deleted_by_sender_at'=>null,'deleted_by_receiver_at'=>Carbon::now()]);
        //we can do this  lazilly setting both to null instaed of trying indentify the correct one that should be set to null
        $parentMessage->deleted_by_sender_at=null;
        $parentMessage->deleted_by_receiver_at=null;
        $parentMessage->save();
        //************************************************************************************* */
        

        
        return back()->with('success','Reply sent');
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
        
         //NOTE: In this function $user refers to sender:

        $this->authorize('cpCreate','BethelChika\Laradmin\UserMessage');

        $this->validate($request, [
            'message' => 'required|string|max:4000',
            'subject' => 'required|string|max:100',
            'email'=>'nullable|email',
            'parent_id'=>'nullable|exists:user_messages'
        ]);
        
        $user=User::findOrFail($this->cpId);//Auth::user();
        $admin=Auth::user();


        $userMessage=new UserMessage;
        $userMessage->admin_creator_user_id=$admin->id;
        $userMessage->id = Uuid::uuid4()->toString();
        if(strlen($request->parent_id)==0){
            $userMessage->parent_id=null;
        }else{
            $userMessage->parent_id=$request->parent_id;
        }
        $userMessage->secret=str_random(40);

        
        $userMessage->creator_user_id=$user->id;//The sneder
        
        $receiver='';
        if($request->filled('user')){
            $receiver=User::findOrFail($request->user);
            $userMessage->user_id=$receiver->id;
        }elseif($request->filled('email')){
            if(User::where('email',$request->email)->count()){
                $receiver=User::where('email',$request->email)->first();//receiver
                $userMessage->user_id=$receiver->id;
                
            }
            else{
                return back()->with('warning','Cannot find the specified user');
            }
        }else{
            return back()->with('warning','Please specify who the message should be sent to.');
        }






        $userMessage->subject=$request->subject;

        $userMessage->message=$request->message;

        // $channels=explode(',',$request->channels);
        
        if(is_array($request->channels)){
            $channels=$request->channels;
        }else{
            $channels=explode(',',$request->channels);
        }
        $userMessage->channels=$channels;

        $emailChennelWorked=false;
        if(in_array('email',$channels)){
            Mail::to($receiver->email)
            ->send(new UserMessageMail($user,$receiver,$userMessage,$admin));
            $emailChennelWorked=true;
        }


        if(in_array('database',$channels)){

            // Check for quotas
            if(!($userMessage->isWithinAdminQuota($user))){
                $request->flash();
                if($emailChennelWorked){
                    return back()->with('warning','Message was sent via email only because SYSTEM USER have reached your message quota limit');
                }else{
                    return back()->with('danger','Message could not be sent because SYSTEM USER have reached your message quota limit');
                }
            }

            if(!($userMessage->isWithinUserQuota($receiver))){
                $request->flash();
                if($emailChennelWorked){
                    return back()->with('warning','Message was sent via email only because your receiver has reached a set message quota limit');
                }else{
                    return back()->with('danger','Message could not be sent because your receiver has reached a set message quota limit');
                }
            }

            //save quotas
            $userMessage->addToQuota($receiver);
            if(!($receiver->is($user))){
                $userMessage->addToQuota($user);
            }

            $userMessage->save(); 
        }


        // if(in_array('database',$channels)){
        //     //dd($userMessage);
        //     $userMessage->save(); 
        // }


        $returnToUrl=$request->get('return_to_url','');
        if($returnToUrl){
            if(strpos(str_replace('http//:','',$returnToUrl),str_replace('http//:','',env('APP_URL')))===false){
            }else{
               return redirect($returnToUrl)->with('success','Message was sent successfully');
            }
        }


        return redirect()->route('cp-user-message-index')->with('success','Message was sent successfully');
    }

    /** 
     * Display the specified resource.
     *
     * @param  int  $message
     * @return \Illuminate\Http\Response
     */
    public function show(UserMessage $message)
    {   
        $this->cpAuthorize();
        
        $this->authorize('cpView',$message);

        $user=User::findOrFail($this->cpId);//Auth::user();
        //$admin=Auth::user();



        //dd($message->is($message));
        //TODO: if a $message has a parent_id then retrieve the perent and assigne it to $message

        /** Check if this message has a parent and show the parent instaed since showing the parent will 
         * include the children
         */
        if($message->parent_id){
            $message=UserMessage::findOrFail($message->parent_id);
        }

        
        
        $messages=UserMessage::where('parent_id',$message->id)->get();//


        //\Illuminate\Support\Facades\DB::enableQueryLog();

        $messages=UserMessage::where(function($query) use ($user,$message){
            $query->where(['user_id'=>$user->id,'deleted_by_receiver_at'=>null])
            ->where('parent_id',$message->id);
        })
        ->orWhere(function($query) use ($user,$message){
                    $query->where(['creator_user_id'=>$user->id,'deleted_by_sender_at'=>null])
                    ->where('parent_id',$message->id);
        })->orWhere('id',$message->id)//This last or includes the parent
        ->orderBy('created_at','desc')
        ->paginate(10);
        
        //dd(\Illuminate\Support\Facades\DB::getQueryLog());

        
        
        /* //remove the deleted ones
        $temp_ums=new Collection;
        foreach($messages as $um){
            if($user->id==$um->user_id ){
                if(!$um->deleted_by_receiver_at){
                    $temp_ums->push($um);
                }        
            }else{
                if(!$um->deleted_by_sender_at){
                    $temp_ums->push($um);
                }  
            }
        }
        $messages=$temp_ums; */
        
        //$messages->prepend($message);
        
        //dd($parentMessage);
        $parentMessage=$message;
        return view('laradmin::cp.message.show',compact('messages','parentMessage','user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  UserMessage  $userMessage
     * @return \Illuminate\Http\Response
     */
    public function edit(UserMessage $userMessage)
    {
        $this->cpAuthorize();
        
        $this->authorize('cpUpdate',$userMessage);
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
        abort(403);
        //$this->authorize('update',$parentMessage);

        //$user=User::findOrFail($this->cpId);//Auth::user();
        //$admin=Auth::user();


        // $this->authorize('update',$user);
        // if(!strcmp($request->mark_as,'read')){
        //     $user->notifications()->where('id','=',$id)->marAsRead();
        // }elseif(!strcmp($request->mark_as,'unread')){
        //     $user->notifications()->where('id','=',$id)->update('read_at',null);
        // }else{
        //     return back()->with('Warning','I am not sure what you want to do with the message.');
        // }
        // return back()->with('success','Message is marked as '.$request->mark_as);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
     public function markAsAjax(Request $request)
     {//TODO: authorize this and all other functions in this class
        $this->cpAuthorize();
        
        $user=User::findOrFail($this->cpId);//Auth::user();
        //$admin=Auth::user();
        
        $message_ids=$request->message_ids;
        //return response()->json($request);
        if(count($message_ids)==0){
            return response()->json([]);
        }
        
        
        foreach($message_ids as $mid){
            $message=UserMessage::where(['id'=>$mid,'user_id'=>$user->id])->first();//NOTE: user_id is the ID of the receiver
            
            $this->authorize('cpUpdate',$message);
            

            if(!strcmp($request->mark_as,'read')){
                if($message){
                    $message->read_at=Carbon::now();
                    $message->save();
                }
            }else{
                if($message){
                    $message->read_at=null;
                    $message->save();
                }
            }

        }

        //read the massages out of databaase again
        $messages=UserMessage::whereIn('id',$message_ids)->get();
        $n2=[];
        foreach($messages as $n){
            $temp['id']=$n->id;
            $temp['read_at']=$n->read_at?$n->read_at:0;

            $n2[]=$temp;
            $temp=[];
        }
        return response()->json($n2);

     }

    /**
     * Remove the specified multiple resource from storage.
     *@param \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function destroys(Request $request)
    {
        $this->cpAuthorize();
        
        $user=User::findOrFail($this->cpId);//Auth::user();
        //$admin=Auth::user();



        $messageIds=explode(',',$request->user_messages_ids);

        /* if(!is_array($messageIds)){
            $messageIds=[$messageIds];
        } */

        $parentId=true;//If this ever becomes false then a parent will have been deleted b/c parents has parentId of null

        foreach($messageIds as $messageId){
            //dd($messageId);
            $message=UserMessage::findOrFail($messageId);

            $this->authorize('cpDelete',$message);

            $parentId=$message->parent_id;


            $childMessages=UserMessage::where('parent_id',$message->id)->get();
            
            $childMessages->push($message);
            
            foreach($childMessages as $um){
                $um->deleteByUser($user);

                
            }
        }

        //If this involves deletion of a parent message, then navigate back message list because there won't be more messages to show in the case of 'show' method
        if(!$parentId){//A parent will have no parent
            return redirect()->route('cp-user-message-index')->with('success','Message deleted');
        }
        return back()->with('success','Message deleted');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \BethelChika\Laradmin\UserMessage
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserMessage $message)
    { 
        $this->cpAuthorize();
        
        $this->authorize('cpDelete',$message);

        $user=User::findOrFail($this->cpId);//Auth::user();
        //$admin=Auth::user();
       

        $parentId=$message->parent_id;//If this ever becomes false then a parent will have been deleted b/c parents has parentId of null


        $messages=UserMessage::where('parent_id',$message->id)->get();
        
        $messages->push($message);
        
        foreach($messages as $um){
            $um->deleteByUser($user);

            
        }
        

        //If this involves deletion of a parent message, then navigate back message list because there won't be more messages to show in the case of 'show' method
        if(!$parentId){//A parent will have no parent
            return redirect()->route('cp-user-message-index')->with('success','Message deleted');
        }
        return back()->with('success','Message deleted');
    }
}
