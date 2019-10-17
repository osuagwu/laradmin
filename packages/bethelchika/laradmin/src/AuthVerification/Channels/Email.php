<?php
namespace BethelChika\Laradmin\AuthVerification\Channels;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use BethelChika\Laradmin\Confirmation;
use BethelChika\Laradmin\LoginAttempt;
use BethelChika\Laradmin\AuthVerification\Contracts\Channel;
use BethelChika\Laradmin\AuthVerification\Mail\EmailChannel;
use BethelChika\Laradmin\AuthVerification\Models\AuthVerification;
use Illuminate\Support\Carbon;
class Email implements Channel{

    /**
     * The unique tag
     *
     * @var string
     */
    private $tag='email';

     /**
     * @inheritDoc
     */
    public function getTag(){
        return $this->tag;
    }

     /**
     * @inheritDoc
     */
    public function getTitle(){
        return 'Email address';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(){
        return 'Send a code to my email address';
    }

  

     /**
     * Perform some maintenance, e.g maintain the confirmation table by deleting previous records
     * @param LoginAttempt $attempt
     * @return void
     */
    private function maintenance(LoginAttempt $attempt){
        $attempt->user->confirmations()->where('type',get_class())->where('type_id',$attempt->id)->delete();

        // Clean other expired confirmations
        $confirmations=Confirmation::where('type',get_class())->get();
        foreach($confirmations as $c){
            if(self::hasCodeExpired($c)){
                $c->delete();
            }
        }
    }

    
    /**
     * Make the input  code a secret
     *
     * @param  string  $value
     * @return string
     */
    private static function code2Secret($value){
        return Hash::make($value);
   }

    /**
     * Get the verification code.
     * @param Confirmation $confirm
     * @return string 
     */
    private static function getCode(Confirmation $confirm){
        return $confirm->user_data;   
    }

        /**
     * Get the confirmation.
     * @param LoginAttempt $attempt
     * @return Confirmation
     */
    private static function getConfirmation(LoginAttempt $attempt){
        // $attempt_id=null;
        // if($attempt->id){
        //     $attempt_id=$attempt->id;
        // }dd($attempt);

        /**
         * Note that if $attempt has not been saved previously(during login or successful 
         * verification), then $attempt->id is null; and therefore any other unsaved attempt
         * for email channel will be returned; i.e. it is possible for an unsaved attempt to 
         * used a Confirmation created for a different unsafe attempt. 
         */
        return $attempt->user->confirmations()->where('type',get_class())->where('type_id',$attempt->id)->first();
        
    }


    /**
     * Make a verification code.
     *
     * @return string The generated code;
     */
    private function makeCode(){
        return rand(11111,99999).'';
        
    }

    /**
     * Has the current code expired
     * @param Confirmation $confirm
     * @return boolean
     */
    private static  function hasCodeExpired(Confirmation $confirm){
        $expiry=config('laradmin.auth_verification_code_expiry');
        if($confirm->created_at->addSeconds($expiry) < Carbon::now()){
            return true;
        }
        return false;
    }


    

    /**
     * Send a code to a user email
     * @param LoginAttempt $attempt
     * @param string $email
     * @return boolean
     */
    public function sendCode(LoginAttempt $attempt,$email){
        
        $this->maintenance($attempt);

        $code=self::makeCode();

        $confirm=new Confirmation();
        $confirm->user_data=self::code2Secret($code);
        $confirm->user_id=$attempt->user->id;
        $confirm->type=get_class();
        
        /**
         * Note: $attempt->id will be null unless the attempt has been used to login or has been 
         * verified previously. This is because attempts are currently only save at login and 
         * after successful verification. If the null is an issue with retrieving the Confirmation
         * later we could simply just save the attempt here so that the $attempt->id is none null.
         * 
         * Also note that the type (above) and type_id here are deliberately referring to different 
         * resources although we can still make both to refer to $attempt.
         */
        $confirm->type_id=$attempt->id;
        
        $confirm->save();
        

        Mail::to($email)
        ->send(new EmailChannel($attempt->user,$code));
        
        return true;
    }

    /**
     * Verify
     *
     * @param AuthVerification $auth_verification
     * @param string $code The code
     * @return boolean|null Null for invalid code
     */
    public function verify(LoginAttempt $attempt,$code){
        $attempt=$attempt->tried();

        $confirm=self::getConfirmation($attempt);
        if(self::hasCodeExpired($confirm)){
            
            return null;
        }
        $current_code=self::getCode($confirm);
        if(!$current_code){
            
            return null;
        }

               
        $re=Hash::check($code,$current_code);

        if(!$re){
            
            return false;
        }

        if(!$attempt->verify($this)){
            return false;
        }
        $confirm->delete();  

        return true;
    }





}