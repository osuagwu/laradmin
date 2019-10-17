<?php

namespace BethelChika\Laradmin\Http\Controllers\User\Traits\AuthVerification;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use BethelChika\Laradmin\AuthVerification\Channels\Password;
use BethelChika\Laradmin\AuthVerification\Contracts\Channel;
use BethelChika\Laradmin\LoginAttempt;

trait PasswordChannel
{
   

    public function password(Request $request){
        $attempt=LoginAttempt::getCurrentAttempt($request);
        
        $password_channel=new Password; 

        if(!$attempt->has2Verify()){
           return $this->intended();
        }
        if(!$attempt->canVerify($password_channel)){
            return redirect()->route('user-auth-v');
        }

        $pageTitle='Password verification';
        return view('laradmin::user.auth_verification.channels.password.index',compact('pageTitle'));


    }


    public function passwordVerify(Request $request){

        $attempt=LoginAttempt::getCurrentAttempt($request);
        
        $password_channel=new Password; 

        if(!$attempt->has2Verify()){
           return $this->intended();
        }
        if(!$attempt->canVerify($password_channel)){
            return redirect()->route('user-auth-v');
        }


        $this->validate($request,[
            'password'=>'required|string|max:200',//note that max is just any big number to avoid extremely long string 
        ]);
        

        $re=$password_channel->verify($attempt,$request->password);


        if($re===null){//KEEP OPEN IF YOU WANT TO REPORT INCORRECT PASSWORD
            return back()->with('danger','Incorrect detail');
        }

        if(!$re){
            return redirect()->route('user-auth-v')->with('danger','Verification failed');
        }

        $pageTitle='Verification is complete';
        return view('laradmin::user.auth_verification.done',compact('pageTitle'));
    }
}