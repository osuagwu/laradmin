<?php

namespace BethelChika\Laradmin\Http\Controllers\User;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use BethelChika\Laradmin\User;
use Illuminate\Http\Request;
use BethelChika\Laradmin\UserMessage;
use Illuminate\Support\Collection;
use BethelChika\Laradmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use BethelChika\Laradmin\Mail\ContactUsUserMessageMail;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class ContactUsUserMessageController extends Controller
{
    
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
     public function __construct()
     {
        parent::__construct();
     }
    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       abort(403);


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //$this->authorize('create',UserMessage::class);

        $parent_id='';
        $request=request();
        if($request->parent_id){
            $parent_id=$request->parent_id;
        }
        

        $returnToUrl=url()->previous();
        //dd($returnToUrl);
        $section_first_bg='https://www.paypalobjects.com/webstatic/en_GB/mktg/wright/partners_and_developers/Partner-Developers-Page_Website-hero.jpg';
        
        
        return view('laradmin::user.message.contact_us.create',compact('parent_id','returnToUrl','section_first_bg'));
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //NOTE: In this function $user referes to the sender
       // $this->authorize('create',UserMessage::class);

        
        $this->validate($request, [
            'message' => 'required|string|max:4000',
            'subject' => 'required|string|max:100',
            'your_email'=>'nullable|email',
            'parent_id'=>'nullable|exists:user_messages'
        ]);
        
        $tempUser=new User;
        $user=$tempUser->getGuestUser();



        $userMessage=new UserMessage;
        $userMessage->id = Uuid::uuid4()->toString();
        if(strlen($request->parent_id)==0){
            $userMessage->parent_id=null;
        }else{
            $userMessage->parent_id=$request->parent_id;
        }
        $userMessage->secret=str_random(40);

        
        $userMessage->creator_user_id=$user->id;//The sneder
        
        
        $receiver=$tempUser->getSystemUser();//FIXME: not urgent but we could allow admin to set who this user can be from control panel. i.e admin a create a special user who gets to receive these emails



        $userMessage->subject=$request->subject;
        $userMessage->your_email=$request->your_email;

        $userInfo='';
        if(!Auth::guest()){
            $userInfo='( Registered user:: ID: '.Auth::user()->id.', Name: '.Auth::user()->name." \n)";
        }
        $names=$request->title.'. '.$request->first_name.' '.$request->last_name."\n";
        $userMessage->message=$names.$userInfo.$request->message;

        $channels=['email'];// OR open this to read from post//$channels=explode(',',$request->channels);
        $userMessage->channels=$channels;
        
        
        if(in_array('email',$channels)){
            $senderEmail=$userMessage->your_email;
            $senderName=$request->first_name.' '.$request->last_name;
            Mail::to($receiver)            
            ->send(new ContactUsUserMessageMail($senderEmail,$senderName,$receiver,$userMessage));
        }



        $returnToUrl=$request->get('return_to_url','');
        if($returnToUrl){
            if(strpos(str_replace('http//:','',$returnToUrl),str_replace('http//:','',env('APP_URL')))===false){
            }else{
               return redirect($returnToUrl)->with('success','Message was sent successfully');
            }
        }


        return back()->with('success','Message was sent successfully');
    }

  

   
}
