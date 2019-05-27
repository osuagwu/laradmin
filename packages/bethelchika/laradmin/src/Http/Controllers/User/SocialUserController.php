<?php

namespace BethelChika\Laradmin\Http\Controllers\User;

use Illuminate\Http\Request;
use BethelChika\Laradmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use BethelChika\Laradmin\Social\SocialUserManager;
use BethelChika\Laradmin\Social\Models\SocialUser;
use BethelChika\Laradmin\Traits\ReAuthController;
use Illuminate\Support\Facades\Log;
use BethelChika\Laradmin\Laradmin;

class SocialUserController extends Controller
{
    use ReAuthController;

    private $socialUserManager;
    private $laradmin;

    public function __construct(SocialUserManager $socialUserManager, Laradmin $laradmin)
    {
        parent::__construct();
       $this->middleware('re-auth')->only('index');
       $this->middleware('auth')->only(['index','unlinkSocialUser']);
       $this->socialUserManager=$socialUserManager;

       $this->laradmin=$laradmin;

       // Load menu item for user settings
       $laradmin->contentManager->loadMenu('user_settings');

       //Register classes
       //$laradmin->assetManager->registerBodyClass('sidebar-white');

       // Set container fluid
       $laradmin->assetManager->setContainerType('fluid');

       
       
       $this->laradmin->assetManager->registerBodyClass('has-minor-nav') ;
    }

    public function index(Request $request){
        $user=Auth::user();
        $this->authorize('update',$user);

        

        $socialUsers=$user->socialUsers()->where('provider','!=','email')->get();
        $pageTitle='Social user account';
        return view('laradmin::user.social_user.index',compact('socialUsers','pageTitle'));
    }

  
    public function unlinkSocialUser(Request $request,SocialUser $socialUser){
        $this->authorize('update',$socialUser->user);//edit ability of the user is required for this operation
        
        switch($this->socialUserManager->unlinkSocialUser($socialUser)){
            case 0:
                return back()->with('danger','Error occured while removing social user account');
                break;
            case 1:
                return back()->with('success', 'Done');
                break;
            default:
                return back()->with('warning','Something went wrong');
        }
    }

    /**
     * Redirect the user to the  provider authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider,$callType='auth')
    {
        // For testing
        // return redirect()->action(
        //     '\BethelChika\Laradmin\Http\Controllers\User\SocialUserController@handleProviderCallback', ['provider' => 'facebook']
        // );
        
        if(!($this->socialUserManager->setProviderCall($callType,$provider))){
            abort(404,'SocialUserController error: Could not set provider call type');
        }
        return Socialite::driver($provider)->redirect();
        
    }

    /**
     * Obtain the user information from provider.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider)
    {
        $danger='There was error trying to link your social account';
        //Socialite::driver($provider)->stateless();
        $rawSocialUser = Socialite::driver($provider)->user();// TODO::  DO validation of the values, perhaps use a static function in SocialUserManager class
        //$rawSocialUser=1;
        

        // Check if no user is returned and redirect based on if user is login in or not
        if(!$rawSocialUser){
            
            if(Auth::check()){
                return redirect()->route('social-user')->with('danger',$danger); 
            }else{
                return redirect()->route('login')->with('danger',$danger);
            }
        }
        

        //$rawSocialUser=SocialUserManager::extractSocialUser($socialUserImport);
        $call=$this->socialUserManager->getAndClearProviderCall();

        if(Auth::check()){
            // User is logged in so we need to check provider call type
            $user=Auth::user();
            $this->authorize('update', $user);

            switch($call[1]){
                case 'link': // We just want to link the social user to the logged in user
                    $linkResult=$this->socialUserManager->linkRawSocialUser($rawSocialUser,$user,$provider);
                    if($linkResult){
                        if($linkResult===-1){//dd($linkResult);
                            
                            //Update token with latests
                            $socialUser=$this->socialUserManager->getMatchByUser($rawSocialUser,$provider,$user);
                            if($socialUser)$this->socialUserManager->updateTokenWithRaw($socialUser,$rawSocialUser,$provider);

                            return redirect()->route('social-user')->with('warning',' It was not possible to link the social account. Please make sure you have not already linked the account or details of it.'); 
                        }
                        return redirect()->intended(route('social-user'))->with('success','Your social account was linked'); 
                    }else{
                        return redirect()->route('social-user')->with('danger',$danger); 
                    }
                    break;
                case 're-auth': // We have access to social user the user used to login: Note that if we pretend that we do not have access to the social user used to login then we can combine this case with the case of 're-auth-match'
                    $socialUser=$this->socialUserManager->getMatchByUser($rawSocialUser,$provider,$user); 
                    $authSocialUser=$this->socialUserManager->getAuth();
                    return $this->reAuthWithSocialUsers($authSocialUser,$socialUser);
                    break;
                case 're-auth-match'://We do not have access to the social user the user used to login
                    //Update details with latests
                    $socialUser=$this->socialUserManager->getMatchByUser($rawSocialUser,$provider,$user);
                    return $this->reAuthWithMatchSocialUser($rawSocialUser,$provider,$this->socialUserManager);
                    break;
                default:
                    Log::warning(__FILE__.'> Line '.__LINE__.': Unknown provider call, '. $call[1]);
                    return redirect()->route('home')->with('warning','Your request was not understood'); 
                

            }
        }else{ 
            switch($call[1]){
                case 'auth':
                    $socialUserClassName=SocialUser::class;
                    // User is not logged in
                    $socialUser=$this->socialUserManager->rawToSocialUser($rawSocialUser,$provider);
                    
                    if( $socialUser instanceof $socialUserClassName){
                        $user=$socialUser->user;
                        Auth::login($user, true);
                        if(!Auth::check()){
                            Log::error(__FILE__.'> Line '.__LINE__.'Message > Error occured while trying to login with your social account please try again');
                            return redirect()->route('login')->with('danger','Error occured while trying to login with your social account please try again'); 
                        }
                        //Check user has no avatar and put one
                        if(!$user->avatar){
                            $user->setAvatar($socialUser->social_avatar);
                        }

                        //Update details with latests
                        $this->socialUserManager->updateWithRaw($socialUser,$rawSocialUser,$provider);
                        

                        //Make a note of the social user used to login and then redirect the user
                        $this->socialUserManager->setAuth($socialUser);
                        return redirect()->intended(route('home')); 
                    }else{
                        Log::warning(\get_class($this->socialUserManager).' > rawToSocialUser > returned('.$socialUser.')');
                        if($socialUser=-1){
                            // Responsd specifically to this return
                        }
                        elseif($socialUser=-2){
                            // Responsd specifically to this return
                        }
                        elseif($socialUser=-3){
                           // Responsd specifically to this return
                        }
                        Log::error(__FILE__.' : > Line '.__LINE__.' : Message > Error occured while trying to login with your social account please try again.');
                        return redirect()->route('login')->with('danger','Error occured while trying to link with your social account please try again'); 
                    }
                    break;
                default:
                    Log::warning(__FILE__.' : > Line '.__LINE__.' : Message > Unknown provider call , '. $call[1]);
                    abort(404,'Unknown provider call, '. $call[1]);


            }
        }
        
    
    }

    /**
     * Shows account control settings
     *
     * @return \Illuminate\Http\Response
     */
    public function externalAccounts(){
        $user=Auth::user();
        $this->authorize('delete', $user);
        $this->laradmin->assetManager->registerBodyClass('sidebar-white') ;
        
        $pageTitle='External accounts';
        return view('laradmin::user.social_user.external_accounts',compact('pageTitle'));
    }

}