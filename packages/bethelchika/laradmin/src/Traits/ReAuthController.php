<?php

namespace BethelChika\Laradmin\Traits;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use BethelChika\Laradmin\Social\Models\SocialUser;
use BethelChika\Laradmin\Social\SocialUserManager;

Trait ReAuthController
{
    //private $socialUserManager;

    /**
     * The maximum allowed numer of reauth attempts. TODO: should be moved to CP settings
     *
     * @var int
     */
    private $MAX_RE_AUTH_COUNT=5;
     /*
     * Create a new controller instance.
     *
    //  * @return void
    //  */
    //  public function __construct()
    //  {
    //      $this->middleware('auth');
    //      //$this->socialUserManager=$socialUserManager;
    //  }


    public function reAuthIndex(SocialUserManager $socialUserManager){
        
        $user=Auth::user();
        $this->authorize('update', $user);
        
        $reAuthRoute=false;
        $authSocialUser=false;
        
        // Check user is infact required to reauth
        if(!$user->isReAuthOn()){
            return redirect()->route('home');
        }

        $authSocialUser=$socialUserManager->getAuth();//Ge the social user used for authentication
        //dd($authSocialUser);
        //$authSocialUser->social_token_expires_in=5182614;
        //$authSocialUser->save();
        //dd($authSocialUser->updated_at->diffForHumans());
        if($authSocialUser){
            $reAuthRoute=route('re-auth-social-user',$authSocialUser->id);
            if($socialUserManager->tokenExpired($authSocialUser)){
                
                $reAuthRoute=route('social-user-callout',[$authSocialUser->provider,'re-auth']);
            }
        }
        $pageTitle ='Confirm password';
        return view('laradmin::user.re_auth.index',compact('pageTitle','authSocialUser','reAuthRoute'));
        
    }

    public function reAuth(Request $request){
        $user=Auth::user();
        $this->authorize('update', $user);

        $user->logReAuthAttempt();

        //Implment validation
        
        $this->validate($request, [  
            'password'=>'required',  
        ]);
        $pass_match=$user->reAuthWithPassword($request->password);//
        
        if(!$pass_match){
            $response=$this->reAuthFail('');
            if($response){ 
                return $response;
            }

            $this->validate($request, [  
                'password'=>'in:'.$request->password.str_random(16),  //WE add string here as a way to force the validation to fail because the password check has already failed
            ]);
            exit(); //Note that this is not neccessary, but incase for any reason the validator above did not return to previous page
        }
        
        //
        //all looks good
        return $this->reAuthSuccess();
    }
    

    public function reAuthWithSocialUser(SocialUserManager $socialUserManager,SocialUser $authSocialUser){
        $user=Auth::user();
        $this->authorize('update', $user);
        
        $user->logReAuthAttempt();
        
        if($authSocialUser){
            
            // $rawSocialUser=$this->socialUserManager->getUserFromToken($authSocialUser);
            // $freshSocialUser=$this->socialUserManager->extractSocialUser($rawSocialUser,$authSocialUser->provider);
            //if($authSocialUser->social_id==$freshSocialUser->social_id){
            if($user->reAuthWithSocialUser($authSocialUser,$socialUserManager,true)){//NOTE: the last boolean when true makes sure that the social user on the database will be updates on success
                //all looks good

                return $this->reAuthSuccess();
               
            }
            else{
                $response=$this->reAuthFail('');
                if($response){ 
                    return $response;
                }    
                return back()->with('danger','Access denied.');
            }
        }
        else{
            $response=$this->reAuthFail('');
            if($response){ 
                return $response;
            }
            return back()->with('danger','Authorisation failed, please try again later.');
        }
        
    }

    public function reAuthWithSocialUsers(SocialUser $authSocialUser,SocialUser $freshSocialUser){
        $user=Auth::user();
        $this->authorize('update', $user);
        
        $user->logReAuthAttempt();
        
        if($authSocialUser){
            
            // $rawSocialUser=$this->socialUserManager->getUserFromToken($authSocialUser);
            // $freshSocialUser=$this->socialUserManager->extractSocialUser($rawSocialUser,$authSocialUser->provider);
            //if($authSocialUser->social_id==$freshSocialUser->social_id){
            if($user->reAuthWithSocialUsers($authSocialUser,$freshSocialUser)){
                //all looks good

                //update details
                $this->socialUserManager->update($authSocialUser,$freshSocialUser);
                return $this->reAuthSuccess();
               
            }
            else{
                $response=$this->reAuthFail('');
                if($response){ 
                    return $response;
                }    
                return back()->with('danger','Access denied.');
            }
        }
        else{
            $response=$this->reAuthFail('');
            if($response){ 
                return $response;
            }
            return back()->with('danger','Authorisation failed, please try again later.');
        }
        
    }
 
    public function reAuthWithMatchSocialUser($rawOrSocialUser,$provider,SocialUserManager $socialUserManager){
        $user=Auth::user();
        $this->authorize('update', $user);
        
        $user->logReAuthAttempt();
        
        if($rawOrSocialUser){
            
            if($user->reAuthWithMatchSocialUser($rawOrSocialUser,$provider,$user,$socialUserManager)){
                $socialUser=$this->socialUserManager->updateMatchWithRaw($rawOrSocialUser,$provider,$user);
                
                //all looks good
                return $this->reAuthSuccess();
               
            }
            else{
                $response=$this->reAuthFail('');
                if($response){ 
                    return $response;
                }    
                return back()->with('danger','Access denied.');
            }
        }
        else{
            $response=$this->reAuthFail('');
            if($response){ 
                return $response;
            }
            return back()->with('danger','Authorisation failed, please try again later.');
        }
        
    }

    private function reAuthSuccess(){
        $user=Auth::user();
        $urlIntended=$user->getReAuthUrlIntended();
        $user->reAuthSuccess();
        return redirect($urlIntended);
    }

    private function reAuthFail($msg=''){
        $user=Auth::user();
        if($user->countReAuthAttempt()>=$this->MAX_RE_AUTH_COUNT){
            $user->reAuthFail(true);
            return redirect()->route('login')->with('danger',$msg.' Maximum number of re-authentication attempt reached. Please login again to continue.');
        }else{
            return false;
        }
    }

    
}
