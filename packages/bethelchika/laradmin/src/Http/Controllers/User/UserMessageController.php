<?php

namespace BethelChika\Laradmin\Http\Controllers\User;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use BethelChika\Laradmin\User;
use BethelChika\Laradmin\Laradmin;
use Illuminate\Http\Request;
use BethelChika\Laradmin\UserMessage;
use Illuminate\Support\Collection;
use BethelChika\Laradmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use BethelChika\Laradmin\Mail\UserMessageMail;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class UserMessageController extends Controller
{
    
    /**TODO:
     * [done] #dispaly sent messages with .sent class and not unread/read css class
     * 
     * [done] #paginate show conversations [no priority]
     * [done] #Order messages so new is top [in index ordering is weird]
     * #[done] in users  listing, add email link that goes to create a message with they users id, not email because we do not want to expose email
     * #[done]Authorise
     * # [done]limit message size, i.e od data, 
     * [done] #add search to index and action bar to index
     *  
     * #[done] Add the reciever name just beside the 'Via' where the sender name is
     * [done]#create message quota limit
     * 
     * #[done]move to user directory
     * * #[done]create CP messaging accessible to all admins
     * #[done]what happens if a user is deleted. eg. prevent the remaning person in the conversation from sending new messages; and display deleted user in plce of deleted user name in conversation
     * #[done]Authorise cp on the user_message table normally: for users authorize by checking disabled?
     * #[done] create 'you've got message' alert
     * #[done] test mailable
     * # [done] move often used functions to UserMessageActions triat
     * #[done]create contact us page which send usermessages by by considering everyone as guest, but for logged in user, the id and name will be in the message.
     * 
     * #[Function cancelled] Do not display  for receiver, messages sent only to email address [might need to move to mysql]
     * #Eager load message with user and sender[not priority at all]
     * 
     * 
     * 
     * 
     * /
    
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
     public function __construct(Laradmin $laradmin)
     {
        parent::__construct();
         $this->middleware('auth');

         $laradmin->assetManager->registerMainNavScheme('primary');
         $laradmin->assetManager->setContainerType('fluid',true);
     }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('views',UserMessage::class);

        $user=Auth::user();
        

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
            ->with('user')
            ->with('sender')
            ->orderBy($order_by,$order_by_dir)
            ->paginate(10);//TODO: [ This function is currently abandoned b/c we do not save in the db any msg sent only to emails]do not select messages sent only to email, might need to move to mysql in other to use  json

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
        return view('laradmin::user.message.index',compact('messages','converses','currentOrder'));


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create',UserMessage::class);

        $parent_id='';
        $request=request();
        if($request->parent_id){
            $parent_id=$request->parent_id;
        }
        
        $userTo='';
        $isSupport=false;
        if($request->has('user')){
            $userTo=$request->user;
            try{//dd($userTo);
                $userTo=User::findOrFail($userTo);
                
            }catch(ModelNotFoundException $ex){;
                $userTo=false;
            }
        }elseif($request->has('support')){
            if(!strcmp($request->support,'support')){
                $isSupport=true;
            }
        }

        $returnToUrl=url()->previous();
        //dd($returnToUrl);
        
        $showChannels=false;
        return view('laradmin::user.message.create',compact('parent_id','userTo','isSupport','showChannels','returnToUrl'));
    }
    /**
     * store reply of a messame.
     *
     * @return \Illuminate\Http\Response
     */
    public function reply(Request $request)
    {   
        //NOTE: In this funcion $user refers to the sender

        $parentMessage=UserMessage::findOrFail($request->parent_id);

        $this->authorize('update',$parentMessage);


        $this->validate($request, [
            'message' => 'required|string|max:10000',
            'subject' => 'required|string|max:140',
            //'parent_id'=>'exists:user_messages' FIXME: open this and make it work
        ]);

        //check that message can be replied to
        if($parentMessage->do_not_reply){
            return back()->with('warning','This message cannot be replied');
        }

        $user=Auth::user();
        
        
        
        
        

        

        //dd($request);
        $userMessage=new UserMessage;
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
            ->send(new UserMessageMail($user,$receiver,$userMessage));
            $emailChennelWorked=true;
        }



        if(in_array('database',$channels)){
            
            // Check for quotas
            if(!($userMessage->isWithinUserQuota($user))){
                $request->flash();
                if($emailChennelWorked){
                    return back()->with('warning','Message was sent via email only because you have reached your message quota limit');
                }else{
                    return back()->with('danger','Message could not be sent because you have reached your message quota limit');
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
            $userMessage->addToQuota($receiver);//->user_message_quota=$receiver->user_message_quota+$messageSize;
            //$receiver->save();
            if(!($receiver->is($user))){
                $userMessage->addToQuota($user);//->user_message_quota=$user->user_message_quota+$messageSize;
            }//$user->save();

            //save
            $receiver->userMessages()->save($userMessage);
        }



        


        //********************************************************************* */
        /* If a user deletes a parent message, he will not see subsequent replies. We 
        * will do a quick lazy temporary fix here; we can do this by  lazily setting both to null 
        * instaed of trying indentify the correct one that should be set to null
        */
        $parentMessage->deleted_by_sender_at=null;
        $parentMessage->deleted_by_receiver_at=null;
        $parentMessage->save();
        //************************************************************************************* */
        

        
        return back()->with('success','Reply is sent');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*
            Note that it is possible for a malicious user to check if an email is registered by trying to
            send a message to the user. We can prevent this by returning a generic message regardless of
            whether the email supplied is registered or not. But we could also prevent this by just 
            preventing know admin users from sending email to users using the users' email.
        */
        $userNotFoundErrorMessage=false;

        //NOTE: In this function $user referes to the sender
        $this->authorize('create',UserMessage::class);

        
        $this->validate($request, [
            'message' => 'required|string|max:4000',
            'subject' => 'required|string|max:100',
            'email'=>'nullable|email',
            'parent_id'=>'nullable|exists:user_messages'
        ]);
        
        $user=Auth::user();



        $userMessage=new UserMessage;
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
            if(!$receiver){ 
                if(!$userNotFoundErrorMessage) 
                    return redirect()->route('user-message-index')->with('info','Done!');
                return back()->with('Cannot find the specified user');
            }
        }elseif($request->filled('email')){
            if(User::where('email',$request->email)->count()){
                $receiver=User::where('email',$request->email)->first();//receiver
                
                
            }
            else{
                if(!$userNotFoundErrorMessage) return redirect()->route('user-message-index')->with('info','Done!');
                return back()->with('Cannot find the specified user');
            }
        }elseif($request->has('support')){//This is OK to remain has in laravel 5.5
            if(!strcmp($request->support,'support')){
                $tempUser=new User;
                $receiver=$tempUser->getSystemUser();
            }
            else{

                return back()->with('warning','Please specify who the message should be sent to.');
            }
        }else{
            //if(!$userNotFoundErrorMessage) return back()->with('Done!');
            
            return back()->with('warning','Please specify who the message should be sent to.');
        }

        $userMessage->user_id=$receiver->id;





        
        





        $userMessage->subject=$request->subject;

        $userMessage->message=$request->message;

        $channels=['database'];// OR open this to read from post//$channels=explode(',',$request->channels);
        $userMessage->channels=$channels;
        
        $emailChennelWorked=false;
        if(in_array('email',$channels)){
            Mail::to($receiver->email)
            ->send(new UserMessageMail($user,$receiver,$userMessage));
            $emailChennelWorked=true;
        }



        if(in_array('database',$channels)){

            // Check for quotas
            if(!($userMessage->isWithinUserQuota($user))){
                $request->flash();
                if($emailChennelWorked){
                    if(!$userNotFoundErrorMessage) return redirect()->route('user-message-index')->with('info','Done!');
                    return back()->with('warning','Message was sent via email only because you have reached your message quota limit');
                }else{
                    if(!$userNotFoundErrorMessage) return redirect()->route('user-message-index')->with('info','Done!');
                    return back()->with('danger','Message could not be sent because you have reached your message quota limit');
                }
            }

            if(!($userMessage->isWithinUserQuota($receiver))){
                $request->flash();
                if($emailChennelWorked){
                    if(!$userNotFoundErrorMessage) return redirect()->route('user-message-index')->with('info','Done!');
                    return back()->with('warning','Message was sent via email only because your receiver has reached a set message quota limit');
                }else{
                    if(!$userNotFoundErrorMessage) return redirect()->route('user-message-index')->with('info','Done!');
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

        
        $returnToUrl=$request->get('return_to_url','');
        if($returnToUrl){
            if(strpos(str_replace('http//:','',$returnToUrl),str_replace('http//:','',env('APP_URL')))===false){
            }else{
                if(!$userNotFoundErrorMessage) return redirect()->route('user-message-index')->with('info','Done!');
               return redirect($returnToUrl)->with('success','Message was sent successfully');
            }
        }

        if(!$userNotFoundErrorMessage) return redirect()->route('user-message-index')->with('info','Done!');
        return redirect()->route('user-message-index')->with('success','Message was sent successfully');
    }

    /** 
     * Display the specified resource.
     *
     * @param  int  $message
     * @return \Illuminate\Http\Response
     */
    public function show(UserMessage $message)
    {   
        $this->authorize('view',$message);

        $user=Auth::user();

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


        $parentMessage=$message;
        return view('laradmin::user.message.show',compact('messages','parentMessage'));
    }

    /**
     * Show the form for editing the specified resource [Note Implemented].
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(UserMessage $userMessage)
    {
        abort(403);
    }

    /**
     * Update the specified resource in storage[Not implemented].
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        abort(403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
     public function markAsAjax(Request $request)
     {
        
        $user=Auth::user();
        
        $message_ids=$request->message_ids;
        //return response()->json($request);
        if(count($message_ids)==0){
            return response()->json([]);
        }
        
        
        foreach($message_ids as $mid){
            $message=UserMessage::where(['id'=>$mid,'user_id'=>$user->id])->first();//NOTE: user_id is the ID of the receiver
            
            $this->authorize('update',$message);

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
        $user=Auth::user();



        $messageIds=explode(',',$request->user_messages_ids);

        /* if(!is_array($messageIds)){
            $messageIds=[$messageIds];
        } */

        $parentId=true;//If this ever becomes false then a parent will have been deleted b/c parents has parentId of null

        foreach($messageIds as $messageId){
            //dd($messageId);
            $message=UserMessage::findOrFail($messageId);

            $this->authorize('delete',$message);

            $parentId=$message->parent_id;


            $childMessages=UserMessage::where('parent_id',$message->id)->get();
            
            $childMessages->push($message);
            
            foreach($childMessages as $um){
                $um->deleteByUser($user);

                
            }
        }

        //If this involves deletion of a parent message, then navigate back message list because there won't be more messages to show in the case of 'show' method
        if(!$parentId){//A parent will have no parent
            return redirect()->route('user-message-index')->with('success','Message deleted');
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
        $this->authorize('delete',$message);

        $user=Auth::user();



        $parentId=$message->parent_id;//If this ever becomes false then a parent will have been deleted b/c parents has parentId of null


        $messages=UserMessage::where('parent_id',$message->id)->get();
        
        $messages->push($message);
        
        foreach($messages as $um){

            /*  The logged in user is the person trying to delete, check if 
            *   she is the sender or receiver and mark delete accordingly
            */
            $um->deleteByUser($user);

            
        }
        

        //If this involves deletion of a parent message, then navigate back message list because there won't be more messages to show in the case of 'show' method
        if(!$parentId){//A parent will have no parent
            return redirect()->route('user-message-index')->with('success','Message deleted');
        }
        return back()->with('success','Message deleted');
    }
}
