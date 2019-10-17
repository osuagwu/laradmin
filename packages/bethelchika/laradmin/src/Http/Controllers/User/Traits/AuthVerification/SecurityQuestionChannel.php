<?php

namespace BethelChika\Laradmin\Http\Controllers\User\Traits\AuthVerification;

use Illuminate\Http\Request;
use BethelChika\Laradmin\LoginAttempt;
use Illuminate\Support\Facades\Validator;
use BethelChika\Laradmin\AuthVerification\Channels\SecurityQuestion;


trait SecurityQuestionChannel
{
    public function securityQuestion(Request $request){
        $user=$request->user();
        $attempt=LoginAttempt::getCurrentAttempt($request);
        
        $security_question_channel=new SecurityQuestion; 

        if(!$attempt->has2Verify()){
           return $this->intended();
        }
        if(!$attempt->canVerify($security_question_channel)){
            return redirect()->route('user-auth-v');
        }


        $answers=$user->securityAnswers;

        $pageTitle='Security question verification';
        return view('laradmin::user.auth_verification.channels.security_question.index',compact(['pageTitle','answers']));


    }

    public function securityQuestionVerify(Request $request){

        $user=$request->user();
        $attempt=LoginAttempt::getCurrentAttempt($request);
        
        $security_question_channel=new SecurityQuestion; 

        if(!$attempt->has2Verify()){
           return $this->intended();
        }
        if(!$attempt->canVerify($security_question_channel)){
            return redirect()->route('user-auth-v');
        }

        $answers=$user->securityAnswers;

        

        // NOTE: This is very important b/c it makes sure that the provided answer id matches that 
        // for the user in db. It protects against an attacker who may change the answer ids in 
        // order to provide answers for other questions which they may have set up themselve.
        $answers_ids= explode(',',$answers->implode('id',','));
        $answers_ids=array_map(function($ar){return intval($ar);},$answers_ids);
        //dd($answers_ids);
        foreach($request->security_answers as $id=> $sa){
            if(!in_array($id,$answers_ids,true)){
                return back()->with('warning','You have not answered the right question');
            }
        }

        // Main validation
        Validator::make($request->all(),[
            'security_answers'=>'required|array|max:'.count($answers),
            'security_answers.*'=>'required|string|max:250',
        ],
        [
            'security_answers.*.max'=>'Security answer must not exceed 250 characters',
            'security_answers.*.required'=>'Security answer is required',
            'security_answers.*.string'=>'Security answer must be a string',
            
                    
        ]
        )->validate();
    

        $re=$security_question_channel->verify($attempt,$request->security_answers);


        if($re===null){
            return back()->with('danger','Incorrect detail');
        }

        if(!$re){
            return redirect(route('user-auth-v').'/channel/security_question')->with('danger','Verification failed. Please make sure the answers are correct');
        }

        $pageTitle='Verification is complete';
        return view('laradmin::user.auth_verification.done',compact('pageTitle'));
    }


}