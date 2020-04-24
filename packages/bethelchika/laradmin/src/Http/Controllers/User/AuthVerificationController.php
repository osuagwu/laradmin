<?php

namespace BethelChika\Laradmin\Http\Controllers\User;

use BethelChika\Laradmin\AuthVerification\AuthVerificationManager;
use Lang;
use Illuminate\Http\Request;
use BethelChika\Laradmin\LoginAttempt;
use BethelChika\Laradmin\Http\Controllers\Controller;
use BethelChika\Laradmin\Http\Controllers\User\Traits\AuthVerification\EmailChannel;
use BethelChika\Laradmin\Http\Controllers\User\Traits\AuthVerification\PasswordChannel;
use BethelChika\Laradmin\Http\Controllers\User\Traits\AuthVerification\SecurityQuestionChannel;
use BethelChika\Laradmin\Http\Controllers\User\Traits\AuthVerification\Xfactor;
use BethelChika\Laradmin\Laradmin;

class AuthVerificationController extends Controller
{

    use EmailChannel, SecurityQuestionChannel, PasswordChannel;
    use Xfactor;
        /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Laradmin $laradmin)
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware('re-auth:5')->only('xfactorUpdate');

        // Set sub app name
        $laradmin->contentManager->registerSubAppName('User manager',route('user-profile'));
    }

     /**
      * Show the verification home
      *
      * @return \Illuminate\Http\Response
      */
    public function index(Request $request){
        $attempt=LoginAttempt::getCurrentAttempt($request);
        
        if(!AuthVerificationManager::has2Verify($attempt)){
            return $this->intended();
        }
        $channels=$attempt->getChannels();
        

        $pageTitle='Verification';
        return view('laradmin::user.auth_verification.index',compact(['pageTitle','channels']));

    }

    /**
     * Process the Channel
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function process(Request $request){
        $attempt=LoginAttempt::getCurrentAttempt($request);;
        
        if(!AuthVerificationManager::has2Verify($attempt)){
            return $this->intended();
        }
        $channels=$attempt->getChannels();

        
        // Extract the tags of the channels
        $channel_tags=[];
        foreach($channels as $channel){
            $channel_tags[]=$channel->getTag();
        }
        
        // Before asking for it in the request check if a channel hasbeen set already
        // if(in_array($auth_verification->channel,$channel_tags)){
        //     $tag=$auth_verification->channel;
        // }else{ }

        $this->validate($request,[
            'channel'=>'required|string|in:'.implode(',',$channel_tags),
        ]);
        $tag=$request->channel;
        //$auth_verification->channel=$request->channel;
        //$auth_verification->save();
        //dd(rtrim(route('user-auth-v'),'/').'/channel/'.$tag);
        return redirect(rtrim(route('user-auth-v'),'/').'/channel/'.$tag);

        // $result=$auth_verification->processChannel($request,$tag);
        // if($result===true){
        //     return $this->intended();
        // }else{
        //     $pageTitle='Verification';
        //     return view('laradmin::user.auth_verification.process',compact(['pageTitle','channels','result']));
        // }

    }

    /**
     * Verification is complete
     * @param Request $request
     * @return @return \Illuminate\Http\Response
     */
    public function done(Request $request){

        $attempt=LoginAttempt::getCurrentAttempt($request);
        if(!AuthVerificationManager::has2Verify($attempt)){
            AuthVerificationManager::onVerificationComplete();
        }
        // Clear variables if we are
        

        // We go ahead and let the user continue but if the verification was not
        // complete then the responsible middleware will handle that normally.
        return $this->intended();
    }

    /**
     * Redirect to where user intended to go.
     * TODO: Not fully implemented
     *
     * @return \Illuminate\Http\Response
     */
    private function intended(){
        return redirect()->intended();//TODO: this intended is not working; we may need to capture and save intended at the first instance
    }



}