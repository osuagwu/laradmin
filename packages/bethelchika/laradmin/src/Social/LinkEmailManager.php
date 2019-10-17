<?php
namespace BethelChika\Laradmin\Social;

use BethelChika\Laradmin\User;
use BethelChika\Laradmin\Confirmation;
use Illuminate\Support\Facades\Mail;
use BethelChika\Laradmin\Social\Models\SocialUser;
use BethelChika\Laradmin\Social\Mail\LinkEmailConfirmation;


class LinkEmailManager{

    /**
     * The value of the type filed in the confirmation table for emails sent from this class for the purpose of confirming linked emails
     *
     * @var string
     */
    public $confirmationEmailType='social_user_link_email_confirm';

    /**
     * The route name for when user click on confirmation link
     *
     * @var string
     */
    public $confirmationRouteName='social-user-link-email-confirm';
    
    /**
     * The length of time in minutes it takes for a confirmation link to expire.
     *
     * @var int
     */
    public $confirmationExpiryTime=24*60;


    /**
     * Link a given email to a users social user account
     *
     * @param string $email
     * @param User $user
     * @param boolean $needConfirmation If this is tru, then confirmation email will be sent 
     * @return int -1 if email is already linked or 1 if all goes well
     */
    public function linkEmail($email,User $user,$needConfirmation=true){
        
        
        
        
        // Check if we have a Social user accunt with this email
        if(SocialUser::where('social_email',$email)->count()){
            return -1;
        }

        // Check the email does not exist int the users table
        if(User::where('email', $email)->count()){
            return -1;
        }


        //Add email in the Social_users table
        $socialUser=new SocialUser;
        $socialUser->user_id=$user->id;
        if($needConfirmation){
            $socialUser->status=-1;//so will need confirmation
        }else{
            $socialUser->status=1;//so no need for confirmation
        }
        $socialUser->provider='email';
        $socialUser->social_id=$email;

        
        $socialUser->social_nickname='';
        $socialUser->social_name='';
        $socialUser->social_email=$email;
        $socialUser->social_avatar='';
        
        $socialUser->save();
        if($needConfirmation)$this->sendConfirmationEmail($socialUser,$user);

        return 1;
    }

    public function linkEmailConfirmation(SocialUser $socialUser,$token,$expiry_time=null){
        if(!$expiry_time){
            $expiry_time=$this->confirmationExpiryTime;
        }
        //$socialUser=SocialUser::where('email',$email);
        $user=$socialUser->user;
        // if($user->count()==1){
        //     $user=$user->first();
        // }
        // else {
        //     $user=false;
        // }
        //dd($user);

        $confirmed=0;
        $confirmation=false;

        if($user){        
            $confirmation=Confirmation::where('user_id','=',$user->id)
                                        ->where('token','=',$token)
                                        ->where('email_to',$socialUser->social_email)
                                        ->where('type','=',$this->confirmationEmailType)->first();
        }
        //dd($confirmation);
        //print $socialUser->social_email;
        //exit();
        //dd($confirmation);
        
        if($confirmation){
            $now=\Carbon\Carbon::now();
            
            $expired=($now>$confirmation->created_at->addMinutes($expiry_time));
            
            if(!strcmp($token,$confirmation->token) and !$expired){            
                //$socialUser=$user->socialUsers()->where('social_id',$email);
                $socialUser->status=1;
                $socialUser->save();
                $confirmed=1;  
            }

            if($confirmed or $expired){
                $confirmation->delete();//delete confirmation 
                //dd('d');
            }
        }
        return $confirmed;
    }

    /**
     * Sends confirmation email
     *
     * @param SocialUser $socialUser
     * @param User $user
     * @return boolean True if all went well otherwis fails
     */
    private function sendConfirmationEmail(SocialUser $socialUser, User $user){
        
        // Clear data if  present
        $old=$user->confirmations()->where('email_to',$socialUser->social_email)
                            ->where('type',$this->confirmationEmailType)
                            ->first();
        if($old){
            $old->delete();
        }
        
        $token= str_random(40);
        $confirmation=new Confirmation;
        $confirmation->token=$token;
        $confirmation->type=$this->confirmationEmailType;
        $confirmation->email_to=$socialUser->social_email;
        $confirmation->user_id=$user->id;

        $confirmation->save();
        
        
        $confirmationLink= route($this->confirmationRouteName,[$socialUser->id,$token]);

        Mail::to($socialUser->social_email)
            ->send(new LinkEmailConfirmation($user,$confirmationLink,$socialUser->social_email));
        return true;
        
    }

    /**
     * Resend confirmation email
     *
     * @param string $email
     * @param User $email
     * @return @see sendConfirmationEmail
     */
    public function resendConfirmationEmail(SocialUser $socialUser,User $user){
        // Clear data if it is present
        $old=$user->confirmations()->where('email_to',$socialUser->social_email)
                                ->where('type',$this->confirmationEmailType)
                                ->first();
        if($old){
            $old->delete();
        }
        return $this->sendConfirmationEmail($socialUser, $user);
    }


    public function unlinkEmail($socialUser,User $user){
        // Check that this is not the primary email
        if(!strcmp($socialUser->social_email,$user->email)){
            return -1;
        }

        return $socialUser->delete();
    }

    /**
     * Set a linked email as primary
     *
     * @param SocialUser $socialUser
     * @param User $user
     * @return mixed Returns -1 when the email is already set to primary, -2 when the email is not confirmed and true if all works but false if other errors occure during data saving 
     */
    public function setPrimaryEmail(SocialUser $socialUser,User $user){
        // CHeck that this is not the primary email already
        //print $socialUser->social_email;
        //print $user->email;
        
        if(!strcmp($socialUser->social_email,$user->email)){
            return -1;
        }
        
        // Check that it is confirmed
        if(!$socialUser->isEmailConfirmed()){
            return -2;
        }

        // make copies of things we need for later to avoide overiting them
       $oldPrimaryEmail=$user->email;
       $isOldPrimaryEmailConfirmed=$user->isEmailConfirmed();

       // Set the new primary email
       $user->email=$socialUser->social_email;
       $saving= $user->save();
       if($saving and !$isOldPrimaryEmailConfirmed){
           $user->confirmEmail();
       }

       // 
       //$socialUser->delete();

        //If it exist, create a new record for the old primary
        if($oldPrimaryEmail){
            //This function should do nothing if the email is already present in the SocialUser.

             $this->linkEmail($oldPrimaryEmail,$user,!$isOldPrimaryEmailConfirmed);
             
       }
       
       return $saving;

   }

//    /**
//     * Check if the email attched to the socialuser is confirmed
//     *
//     * @param SocialUser $social_user
//     * @return boolean True if the email is confirmed
//     */
//    public static function isEmailConfirmed(SocialUser $social_user){
//        return $social_user->isEmailConfirmed();
        
//    }
   
}