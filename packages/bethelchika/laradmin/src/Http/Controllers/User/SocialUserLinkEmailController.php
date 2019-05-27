<?php

namespace BethelChika\Laradmin\Http\Controllers\User;

use Illuminate\Http\Request;
use BethelChika\Laradmin\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use BethelChika\Laradmin\Social\LinkEmailManager;
use BethelChika\Laradmin\Social\Models\SocialUser;
use BethelChika\Laradmin\Laradmin;

class SocialUserLinkEmailController extends Controller
{
    private $linkEmailManager;

    public function __construct(LinkEmailManager $linkEmailManager, Laradmin $laradmin)
    {
        parent::__construct();
        $this->middleware('auth', ['except' => ['linkEmailConfirmation']]);
        $this->middleware('re-auth')->only(['index']);
       $this->linkEmailManager=$linkEmailManager;

       // Load menu item for user settings
       $laradmin->contentManager->loadMenu('user_settings');

       //Register classes
       $laradmin->assetManager->registerBodyClass('sidebar-white');
       
       // Set container fluid
       $laradmin->assetManager->setContainerType('fluid');
    }

    public function index(Request $request){
        $user=Auth::user();
        $this->authorize('view',$user);
        $socialUsers=$user->socialUsers()->where('social_email','!=',null)->get();

        //Add the primary email to the collection of socialUsers if it exists
        if($user->email){
            $noPrimaryEmail=true;//
            foreach($socialUsers as $socialUser){
                if(!strcmp($socialUser->social_email,$user->email)){
                    $noPrimaryEmail=false;
                    break;
                }
            }

            if($noPrimaryEmail){
                $primarySocialUser=new SocialUser;
                $primarySocialUser->provider='email';
                $primarySocialUser->social_email=$user->email;
                $primarySocialUser->id=0;//make ID as this is not in the social_users table
                $primarySocialUser->status=$user->status;
                $socialUsers->prepend($primarySocialUser);
            }
        
        }

        $pageTitle='Linked email addresses';

        return view('laradmin::user.social_user.link_email_index',compact('socialUsers','pageTitle'));
    }

    public function store(Request $request){
        $user=Auth::user();
        $this->authorize('update',$user);

        $this->validate($request,[
            'email'=>'required|email'
        ]);

        $re=$this->linkEmailManager->linkEmail($request->email,$user);

        if($re===-1 or $re==0){
            return back()->with('warning','Error linking email');
        
        }elseif($re===1){
            return back()->with("success",'Please check your inbox to confirm the email. Unconfirmed email will be removed from your account');

        }
        
    }

    public function destroy(SocialUser $socialUser){
        $user=Auth::user();

        $this->authorize('update',$user);
        $re=$this->linkEmailManager->unlinkEmail($socialUser,$user);
        
        
        
        if($re===-1){
            return back()->with('danger','You cannot delete a primary email');
        }
        elseif($re){
            return back()->with("success",' Done');
        }
        else{
            
            return back()->with('warning','Error deleting email');
        }

    }

    public function setPrimaryEmail(SocialUser $socialUser){
        $user=Auth::user();
        $this->authorize('update',$user);

        $re=$this->linkEmailManager->setPrimaryEmail($socialUser,$user);
        
        
        if($re===-1){
            return back()->with('danger','Email was already set as primary');
        }elseif($re===-2){
            return back()->with('danger','Cannot set unconfirmed email as primary');
        }elseif($re){
            return back()->with("success",' Done');
        }
        else{
            
            return back()->with('warning','Error while setting primary email');
        }

    }

    public function linkEmailConfirmation(SocialUser $socialUser,$key){
        $re=$this->linkEmailManager->linkEmailConfirmation($socialUser,$key);

        if(Auth::check()){
            if($re){
                return redirect()->route('social-user-link-email')->with("success",'Email confirmed');
            }{
                return redirect()->route('social-user-link-email')->with('danger','There was an unspecified error. Please try again.');
            }
        }else{
                $pageTitle='Email confirmation';

            if($re){
                session()->flash("success",'Email confirmed');
                return view('laradmin::user.social_user.link_email_confirmation_result',compact('pageTitle'));
            }{
                session()->flash('danger','There was an unspecified error. Please try again.');
                return view('laradmin::user.social_user.link_email_confirmation_result',compact('pageTitle'));
            }
            
        }
    }

    public function resendConfirmationEmail(SocialUser $socialUser){
        $user=Auth::user();
        $this->authorize('update',$user);
        $re=$this->linkEmailManager->resendConfirmationEmail($socialUser,$user);

        if($re){
            return back()->with("success",'Please check your inbox to confirm the email. Unconfirmed email will be removed from your account');
        }{
            return back()->with('danger','There was an unspecified error.');
        }

    }


  
   

}