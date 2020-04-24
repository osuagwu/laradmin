<?php

namespace BethelChika\Laradmin\Http\Controllers\User\Traits\AuthVerification;

use Illuminate\Http\Request;
use BethelChika\Laradmin\LoginAttempt;
use Illuminate\Support\Facades\Validator;
use BethelChika\Laradmin\AuthVerification\Channels\Email;
use BethelChika\Laradmin\AuthVerification\AuthVerificationManager;



trait EmailChannel
{
    public function email(Request $request){
        
        $attempt=LoginAttempt::getCurrentAttempt($request);
        
        $email_channel=new Email; 

        if(!AuthVerificationManager::has2Verify($attempt)){
           return $this->intended();
        }
        if(!$attempt->canVerify($email_channel)){
            return redirect()->route('user-auth-v');
        }

        $emails=$request->user()->getConfirmedEmails();
        $masked_emails=[];

        foreach($emails as $email){
            $masked_emails=[$email['id']=>$email['masked_email']];
        }

        $pageTitle='Email verification';
        return view('laradmin::user.auth_verification.channels.email.index',compact(['pageTitle','masked_emails']));


    }

    public function emailSendCode(Request $request){
        
        $attempt=LoginAttempt::getCurrentAttempt($request);
        
        $email_channel=new Email; 

        if(!AuthVerificationManager::has2Verify($attempt)){
           return $this->intended();
        }
        if(!$attempt->canVerify($email_channel)){
            return redirect()->route('user-auth-v');
        }

        $this->validate($request,[
                                    'email_id'=>'required|integer|max:250000000'//The max is just random to make some limit of some sort.
                                ]);
        

        $email=$request->user()->getConfirmedEmailById($request->email_id);
        if(!$email){
            return redirect()->route('user-auth-v')->with('warning','There was an issue with a selected email');
        }


        $email_channel->sendCode($attempt,$email['email']);

        
        

        return redirect()->route('user-auth-v-email-step3',[$request->email_id]);
        

    }


    /**
     * Process the code a user have entered. This is the step 3.
     *
     * @param Request $request
     * @param integer $email_id The id of the email address the code was sent to.
     * @return \Illuminate\Http\Response
     */
    public function emailCode(Request $request,$email_id){

        $attempt=LoginAttempt::getCurrentAttempt($request);
        
        $email_channel=new Email; 

        if(!AuthVerificationManager::has2Verify($attempt)){
           return $this->intended();
        }
        if(!$attempt->canVerify($email_channel)){
            return redirect()->route('user-auth-v');
        }




        // If email_id is valid we assume that someone is messing the url and restart the process
        $validator=Validator::make(['email_id'=>$email_id],[
                                    'email_id'=>'required|integer|max:250000000'
                                ]);

        if($validator->fails()){
            return redirect(route('user-auth-v').'/channel/email');
        }

        $email=$request->user()->getConfirmedEmailById(intval($email_id));
        $masked_email=$email['masked_email'];

        $pageTitle='Email verification';
        return view('laradmin::user.auth_verification.channels.email.code',compact(['pageTitle','email_id','masked_email']));

    }


    public function emailVerify(Request $request){
        $attempt=LoginAttempt::getCurrentAttempt($request);
        
        $email_channel=new Email; 

        if(!AuthVerificationManager::has2Verify($attempt)){
           return $this->intended();
        }
        if(!$attempt->canVerify($email_channel)){
            return redirect()->route('user-auth-v');
        }

        $this->validate($request,[
                                    'code'=>'required',
                                    //'email_id'=>'required|integer|max:250000000'
                                ]);

        // If email_id is valid we assume that someone is messing the url and restart the process
        $validator=Validator::make($request->only('email_id'),[
                                    'email_id'=>'required|integer|max:250000000'
                                ]);

        if($validator->fails()){
            return redirect(route('user-auth-v').'/channel/email');
        }
        

        $re=$email_channel->verify($attempt,$request->code);


        if($re===null){
            return redirect()->route('user-auth-v')->with('danger','Verification failed');
        }

        if(!$re){
            return redirect()->route('user-auth-v-email-step3',[$request->email_id])->with('danger','Invalid details');
             
        }
        
        

        $pageTitle='Verification is complete';
        return view('laradmin::user.auth_verification.done',compact('pageTitle'));
    }
}