<?php
namespace BethelChika\Laradmin\Social;

use Carbon\Carbon;
use BethelChika\Laradmin\User;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use BethelChika\Laradmin\Social\Models\SocialUser;
//use Illuminate\Support\Facades\Auth as MainAuth;

class SocialUserManager{
    /**
     * The list of acceptable calls to provider
     *
     * @var array
     */
    private $providerCallTypes=[
        'auth',//to authenticate and login a user 
        'link',//to link a social account
        're-auth', //to reauthenticate a user who currently logged in
        're-auth-match' //Used when social user is not used to login or the authenticated social user is missing
    ];

   /**
    * Names of supported socials
    *
    * @var array
    */
    private $providers=[
        'facebook',
        'google',
        //'twitter',
        //'yahoo',
        //'linkedin',
        //'github',
        //'bitbucket',
        'email',
    ];
    /**
     * Names of social user importers
     *
     * @var array
     */
    private $managers=[
        'socialite',
        'email',
    ];

    /**
     * Name of social user importer to use
     *
     * @var string
     */
    public $manager='socialite';
//TODO: most of these should be moved to settings
   /**
    * Allows signing with a different provider when the email matches that registered from another provider. E.g If a user is registered with an email, allow the user to login with facebook if the emails for thr user from both providers are the same
    *
    * @var boolean
    */
    private $interProviderEmailAccess=true;

    /**
     * When true, if a provider->social_email matches email-provider->socialemail or user->email, both will be automatically linked
     *
     * @var boolean
     */
    private $linkProviderToUserOnEmailMatch=true; 
    /**
     * If an uncofirmed email is  the same as an email we are trying to register, what should we do; 
     * True=release the email so that the current user can use the email to register, (2) false= the current registeration will fail because email is taken but the potential user can try again later by which time an automatic process would have deleted the unconfirmed email
     *
     * @var boolean
     */
    private $releaseUnconfirmedEmail=true;

    /**
     * The time in seconds before a social token from provider is considered to expire. Note that this is diff from that from the provider; we use it to force expiry to overide that from provider. So set this to low number of seconds if you want the token to qickly expire so that a fresh call to provider can be made 
     * TODO: move to settings
     * 
     * @var integer
     */
    private $socialTokenExpire=5*60;

    /**
     * CHecks in a static context if a user can login  using a given provider paramenters
     *
     * @param string $provider
     * @param string $email
     * @param string $socialId The users id from this social media, default=null
     * @return boolean|null|int False is login is not allowed, null is details not found and user ID if all is good 
     */
    public static function canLogin0($provider,$email,$socialId=null){
        // Get the credentials
        if($socialId and $email){
            $socialUser=SocialUser::where('social_id',$socialId)->where('email',$email)->where('provider',$provider)->first();
        //}elseif($sociaId){
            //$socialUser=SocialUser::where('social_id',$socialId)->where('social_name',$social)->first();
        }else{
            $socialUser=SocialUser::where('email',$email)->where('provider',$provider)->first();
        }

        // Check if login is enabled
        if($socialUser){
            if($socialUser->login_enabled){
                //$user=User::findOrFail($socialId->user_id);
                //return MainAuth::attempt($user->email,$user->password);
                return $socialId->user_id;
            }else{
                return false;// return false if login is not enabled
            }

        }else{
            return null;// return null if details is not found
        }

    }
    /**
     * CHecks if a user can login  using a given social account
     *
     * @param SocialUser $socialUser
     * @return boolean False if login is not allowed,
     */
    public function canLogin(SocialUser $socialUser){
        // Check if login is enabled
        //if($socialUser){
            if($this->login_enabled){
                //$user=User::findOrFail($socialId->user_id);
                //return MainAuth::attempt($user->email,$user->password);
                return true;
            }else{
                return false;// return false if login is not enabled
            }
    }

    /**
     * Links a social user from raw data, will perform checks to avoid duplicateds
     *
     * @param $rawSocialUser
     * @param User $user
     * @return @see $this->linkSocialUser() and additionally returns -1 if the data is already linked
     */
    public function linkRawSocialUser($rawSocialUser,User $user,$provider){
        $socialUser=$this->extractSocialUser($rawSocialUser,$provider);

        if(!$socialUser)return false;

        // Check if we have this Social user account linked to the details
        $su=SocialUser::where('provider', $socialUser->provider)
        ->where('social_id', $socialUser->social_id)
        ->first();

        if($su){
            return -1;
        }

        /////////////////////////////////////////////////////////////////////

        // NOW we will perform more check before the linking
        $su=$this->getMatchByOnlySocialEmail($socialUser);
        if($su){
            if(!strcmp($su->provider,'email')){
                // Lets exclude emails that are not from a a social provider. We should later consider emails that are not from social provider separate (Those from social provider should be more reliable). 
                $su=false;
            }
        }
        
        if($su){
            $linkEmailManager=new LinkEmailManager;
            $isConfirmed=$linkEmailManager->isEmailConfirmed($su);//Note: Confirmation is unneccessary here b/c Social accounts are by definition confirmed

            if($isConfirmed  and !$su->user->is($user)){//stop if this acoount has been confirmed by another person
                return -1;//the associated email to the social account has been confirmed by another user.
            }

            if($isConfirmed and $this->interProviderEmailAccess){
                //
            }else{
                //user with the email is present, see if we need to realse the email
                if(!$isConfirmed and $this->releaseUnconfirmedEmail){
                    $this->socialUserUnlink($su);    
                    if($this->getMatchByOnlySocialEmail($socialUser)){
                        return -1;
                    }
                }else{
                    return -1;// There is a social email already. 
                }
                
            }
            
        }

        // Now we can consider non-provider emails (i.e we coniser linked emails) 
        $su=$this->getMatchByOnlySocialEmail($socialUser);
        if($su){
                if(strcmp($su->provider,'email')){
                // Exclude email from social provider (i.e email that is not just a linked email) because they are considered separately 
                $su=false;
            }
        }
        if($su){
            $linkEmailManager=new LinkEmailManager;
            $isConfirmed=$linkEmailManager->isEmailConfirmed($su);

            if($isConfirmed  and !$su->user->is($user)){//stop if this acoount has been confirmed by another person
                return -1;//the associated email to the social account has been confirmed by another user.
            }

            if(!$isConfirmed){
                //SHould we release the email since it is unconfirmed
                if($this->releaseUnconfirmedEmail){
                    $this->socialUserUnlink($su);
                    if($this->getMatchByOnlySocialEmail($socialUser)){
                        return -1;
                    }
                }else{
                    //we halt , the user can try gain when an automatic process may have cleaned unconfirmed email
                    return -1; //  There is linked email already. 
                }  
            }else{
                //user with the email is present, see if we need to realse the email
                if($this->linkProviderToUserOnEmailMatch){
                    //return $this->linkSocialUser($socialUser,$su->user);   
                }else{
                    return -1;// //The social Email exists social users table as a liked email but admin do not allow it to be linked to provider 
                }
                
            }
            
            
        }
        

        // Now we consider  user registered email
        $registered_user = User::where('email', $socialUser->social_email)->first();
        
        if($registered_user  ){
            if (!$registered_user->isEmailConfirmed()){
                
                //SHould we release the email since it is unconfirmed
                if($this->releaseUnconfirmedEmail){
                    $registered_user->releaseEmail();//TODO: ? Has this been implemented
                    if(User::where('email', $socialUser->social_email)->count()){
                        return -1;
                    }
                }else{
                    //we halt , the user can try gain when an automatic process may have cleaned unconfirmed email
                    return -1; // The social email exists in users table but it is not confirmed
                }
            }else{
                if(!$registered_user->is($user)){//stop if this acoount has been confirmed by another person
                    return -1;//the associated email to the social account has been confirmed by another user.
                }

                if($this->linkProviderToUserOnEmailMatch ){
                    //do nothing here because we willdo something at the end
                }else{
                    return -1;//The social Email exists in users table but admin do not allow it to be linked to provider 
                }
                
            }
        }

        /////////////////////So since we got here we can just savely line////////////////////////////////
        return $this->linkSocialUser($socialUser,$user);
    }

     /**
     * Links a social credential to a username. NOte taht the calling function is responsible for checking for the suitability of the linkage
     *
     * @param SocialUser $socialUser
     * @param User $user
     * @return SocialUser or null.
     */
    private function linkSocialUser(SocialUser $socialUser,User $user){
        //$data['provider']=$socialUser->provider;
        //$data['social_email']=$socialUser->social_email;
        //$data['social_id']=$socialUser->social_id;
        // $socialUser=SocialUser::where('provider',$socialUser->provider)
        //             ->where('social_email',$socialUser->social_email)
        //             ->where('social_id',$socialUser->social_id)
        //             ->first();
        // if($socialUser){
        //     return -1;
        // }else{
        //     return $socialUser->save();
        // }
        // return SocialUser::firstOrCreate($this);
        $socialUser->user_id=$user->id;
        $socialUser->save();
        return $this->getMatch($socialUser);
    }
    
    /**
     * Enable or disable login in with the social credentials
     *
     * @param int $loginEnabled {1=>enabled, 0=>disbled}
     * @return void boolean
     */
    public function loginEnabled($loginEnabled=1){
        $this->login_enabled=$loginEnabled;
        return $this->save();

    }

    /**
     * Registeres a social user in the User model. TODO: This funtion to moved to user model or its traits, this might be better moving only the $user->save() function below
     *
     * @param SocialUser $socialUser
     * @return User
     */
    public function register(SocialUser $socialUser){
        
        $user=new User;
        $user->name=$socialUser->social_nickname;
        $user->email=$socialUser->social_email;
        $user->status=1;
        $user->is_active=1;
        
        //$user->password=bcrypt(str_random(40));
        //$user->is_password_auto_gen=1;
                                
        $user->save();// TODO: This should be done by passing temp user to a function in the user model. This is to avoid issues when the fields in the user models change, But it might be better to just move the entire function as stated above
        return $user;
        

    }




  
    /**
     * Returns a SocialUser corresponding the given details of raw social user from e.g socialite, the dtails will be linked and registered if they are missing from the tables
     *
     * @param $rawSocialUser
     * @return mixed SocialUser|integer or other results that evaluate to false. 
     *   Integer return value meanings: 
     *  -1= There is a social email already.
     *  -2= There is linked email already. 
     *  -3= The social Email exists in social users table as a liked email but admin do not allow it to be linked to provider 
     *  -4= The social email exists in users table but it is not confirmed
     *  -5= The social Email exists in users table but admin do not allow it to be linked to provider 
     *  
     */
    public function rawToSocialUser($rawSocialUser,$provider){
        //$su=$this->getMatch($socialUser,$provider);
        //$su=$this->getMatchByOnlySocialEmail($socialUser,$provider);
        //$su=$this->getMatchByOnlyUsersEmail($socialUser,$provider);
        
        $socialUser=$this->extractSocialUser($rawSocialUser,$provider);
        
        if(!$socialUser)return false;

        // Check if we have a Social user account linked to the details. This is the most reliable way to obtain the user
        if(!$socialUser->social_email){
            $socialUser->social_email=null;
        }
        // $su=SocialUser::where('provider', $socialUser->provider)
        //                     ->where('social_id', $socialUser->social_id)
        //                     ->where('social_email',$socialUser->social_email)
        //                     ->first();
        $su=$this->getMatch($socialUser);
        if($su){
            return $su;
        }

        // $su=SocialUser::where('social_email', $socialUser->social_email)
        //                 ->first();
        // Ok now we will try a slightly 'less reiable' (so to speak because we will only concentrate on the email) way to obtain the user
        // Check if the user has (1) registered through our website or (2) linked an email or (3) a social account from a different provider but with same email as the one they are trying to log on with now
        //CAUTION: But in case the person used the same email for more than one provider e.g. facebook and google, we will here accept them loging in with facebook or google since we will be looking at only email here.
       
        $su=$this->getMatchByOnlySocialEmail($socialUser);

        if($su){
                if(!strcmp($su->provider,'email')){
                // Lets exclude emails that are not from a a social provider. We should later consider emails that are not from social provider separate (Those from social provider should be more reliable). 
                $su=false;
            }
        }

        if($su){
            
            $linkEmailManager=new LinkEmailManager;
            $isConfirmed=$linkEmailManager->isEmailConfirmed($su);//FIXME: this will lead to an error
            if($isConfirmed and $this->interProviderEmailAccess){
                return $this->linkSocialUser($socialUser,$su->user);
            }else{
                //user with the email is present, see if we need to realse the email
                if(!$isConfirmed and $this->releaseUnconfirmedEmail){
                    $this->socialUserUnlink($su); 
                    if($this->getMatchByOnlySocialEmail($socialUser)){
                        return -1;
                    }   
                }else{
                    return -1;// There is a social email already. 
                }
                 
            }
        }

        // Now we can consider non-provider emails (i.e we coniser linked emails) 
        $su=$this->getMatchByOnlySocialEmail($socialUser);
        
        if($su){
                if(strcmp($su->provider,'email')){
                // Exclude email from social provider because they are considered separately 
                $su=false;
            }
        }
        
        if($su){            
            $linkEmailManager=new LinkEmailManager;
            $isConfirmed=$linkEmailManager->isEmailConfirmed($su);
            if(!$isConfirmed){
                //SHould we release the email since it is unconfirmed
                if($this->releaseUnconfirmedEmail){
                    $this->socialUserUnlink($su);
                    if($this->getMatchByOnlySocialEmail($socialUser)){
                        return -2;
                    }
                }else{
                    //we halt , the user can try gain when an automatic process may have cleaned unconfirmed email
                    return -2; //  There is linked email already. 
                }  
            }else{
                //user with the email is present, see if we need to realse the email
                if($this->linkProviderToUserOnEmailMatch){
                    return $this->linkSocialUser($socialUser,$su->user);   
                }else{
                    return -3;// //The social Email exists social users table as a liked email but admin do not allow it to be linked to provider 
                }
                 
            }
        }
        

        // Now we consider  user registered email
        $user = User::where('email', $socialUser->social_email)->first();
        
        if($user  ){
            if (!$user->isEmailConfirmed()){
                
                //SHould we release the email since it is unconfirmed
                if($this->releaseUnconfirmedEmail){
                    $user->releaseEmail();//TODO: ? Has this been implemented
                    if(User::where('email', $socialUser->social_email)->count()){
                        return -4;
                    }
                }else{
                    //we halt , the user can try gain when an automatic process may have cleaned unconfirmed email
                    return -4; // The social email exists in users table but it is not confirmed
                }
            }else{
                if($this->linkProviderToUserOnEmailMatch){
                    return $this->linkSocialUser($socialUser,$user);
                }else{
                    return -5;//The social Email exists in users table but admin do not allow it to be linked to provider 
                }
                
            }
        }
        
      

        // So no account is available, so we need to register and link. This method is also very reliable because we ar registering the user our self
        $socialUser->social_nickname=$this->deriveNickname($socialUser);

        // Try to split names into first and second name
        $names=explode(' ',$socialUser->social_name);
        $temp_user['first_names']=$names[0];
        if(count($names)>1){  
            array_shift($names);
            $temp_user['last_name']=implode($names);
        }

        $temp_user['name']=$socialUser->social_nickname;
        $temp_user['email']=$socialUser->social_email;
        
        $user=$this->register($socialUser);
        $this->linkSocialUser($socialUser,$user);

        return $socialUser;
    }

    private function deriveNickname(SocialUser $socialUser){
        $social_nickname=$socialUser->social_nickname;
        if(!$social_nickname){
            $social_nickname=$socialUser->social_name;
        }
        if(!$social_nickname){
            $social_nickname=$socialUser->social_id;
        }
        if(!$social_nickname){
            $social_nickname=$socialUser->social_email;
        }
        if(!$social_nickname){
            $social_nickname='Unnamed '.strtoupper($socialUser->provider).' account';
        }
        return $social_nickname;
    }

    /**
     * COnverts raw social user a user, but the user is not save in the database yet
     *
     * @param mixed $rawSocialUser
     * @return SocialUser
     */
    private function extractSocialUser($rawSocialUser,$provider){
        //if(in_array($this->manager,$this->managers)){
            
            switch($this->manager){
                case 'socialite':
                    return $this->extractSocialiteUser($rawSocialUser,$provider);
                    break;
                default:
                    session()->flash('danger','Unexpected social user manager');
                    return false;
            }
        //}
    }

    /**
     * COnverts a user imported from social-te to a social user model. Note that this social user is not save in the database yet
     *
     * @param array $rawSocialUser
     * @return SocialUser
     */
    private function extractSocialiteUser($rawSocialUser,$provider){
        //return $this->testData($provider);//TESTING:: remove line after testing


        $socialUser=new SocialUser;
        $socialUser->provider=$provider;
        //dd($rawSocialUser);

        // OAuth Two Providers 
        if(isset($rawSocialUser->token)){
            $socialUser->social_token=$rawSocialUser->token;
        }
        if(isset($rawSocialUser->refreshToken)){
            $socialUser->social_token_refresh=$rawSocialUser->refreshToken;// not always provided
        }
        if(isset($rawSocialUser->expiresIn)){
            $socialUser->social_token_expires_in=$rawSocialUser->expiresIn;
        }
        
        // OAuth One Providers
        if(isset($rawSocialUser->tokenSecret)){
            $socialUser->social_token_secret=$rawSocialUser->tokenSecret;
        }
        
        
        // All Providers
        $socialUser->social_id=$rawSocialUser->getId();
        $socialUser->social_nickname=$rawSocialUser->getNickname();
        $socialUser->social_name=$rawSocialUser->getName();
        $socialUser->social_email=$rawSocialUser->getEmail();
        if(strlen($socialUser->social_email)==0){
            $socialUser->social_email=null;
        }
        $socialUser->social_avatar=$rawSocialUser->getAvatar();
        return $socialUser;

    }
    /**
     * Returns test data to simulate working with SOcialUser
     *
     * @param string $manager
     * @return SOcialUser
     */
    function testData($manager='socialite',$provider='twitter'){
        $socialUser=new SocialUser;
        $socialUser->provider=$provider;

        // OAuth Two Providers 
        $socialUser->social_token='iuehgfno893f3';
        
        $socialUser->social_token_refresh=58700967;
        $socialUser->social_token_expires_in=6;
        
        
        // OAuth One Providers
        $socialUser->social_token_secret='ihyuiuyyiuy';
               
        
        // All Providers
        $socialUser->social_id=9786;
        $socialUser->social_nickname='BC';
        $socialUser->social_name='chika Osuagwu';
        $socialUser->social_email='test@test25.com';
        $socialUser->social_avatar='https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSOfzxTaGAGPFEJHUPHeuCpbvYH0kEW0hmCIrrw2sx8vjQJiCZe';
        return $socialUser;
    }

    public function getSocialUsersByUser(User $user){
        //$socialUsers=SocialUser::where('user_id',$user->id);
        return $user->socialUsers;
    }
    public function unlinkSocialUser(SocialUser $socialUser){
        //TODO:: check to warn about unlinking the last
        return $socialUser->delete();
    }


    /**
     * Deletes a social user.
     *
     * @param SocialUser $socialUser
     * @return boolean True if all goes well but flase otherwise
     */
    public function socialUserUnlink(SocialUser $socialUser){
        return $socialUser->delete();

    }

    /**
     * Set currently authenticated social user. Call with empty false to set currently ath social user to false
     *
     * @param SocialUser $socialUser
     * @return void
     */
    public function setAuth($socialUser=false){
        if($socialUser==false){
            Session::forget('auth_social_user_id');// TODO: remove auth_social_user_id in the users table
        }
        Session::put('auth_social_user_id',$socialUser->id);// TODO: store auth_social_user_id in the users table
    }
    /**
     * Return the currently authenticated social user
     *
     * @return mixed SocialUser or false 
     */
    public function getAuth(){
        $id=Session::get('auth_social_user_id',false);// TODO: read auth_social_user_id in the users table
        if($id){
            return SocialUser::find($id);
        }
        else{
            return false;
        }
    }

    /**
     * Retrieving User Details From A Token
     * i.e If you already have a valid access token for a user, you can retrieve their details.
     * @param SocialUser $socialUser
     * @return SocialUser | false 
     */
    public function refreshSocialUserFromToken(SocialUser $socialUser){//
        //return $this->extractSocialUser($socialUser,$socialUser->provider);//TODO:: remove line after testing
        if($this->tokenExpired($socialUser)){
            return false;
        }else{
            $user = Socialite::driver($socialUser->provider)->userFromToken($socialUser->social_token);
            $freshSocialUser=$this->extractSocialUser($user,$socialUser->provider);

            //$this->updateToken($socialUser,$freshSocialUser);//
            return $freshSocialUser;

        }
       
        

    }

    /**
     * Update the  token from provider for the first input from the second input. 
     *
     * @param SocialUser $socialUser
     * @param SocialUser $socialUser2
     * @return boolean True on success
     */
    public function updateToken(SocialUser $socialUser1,SocialUser $socialUser2){
        if($socialUser2->social_token_refresh){
            $socialUser1->social_token_refresh=$socialUser2->social_token_refresh;
        }
        if($socialUser2->social_token){
            $socialUser1->social_token=$socialUser2->social_token;

        }$socialUser1->social_token_refresh=null;
        return $socialUser1->save();
    }

     /**
     * Update the user token from raw social user from provider. 
     *
     * @param SocialUser $socialUser
     * @param array $rawSocialUser
     * @param string $provider
     * @return @see updateToken
     */
    public function updateTokenWithRaw(SocialUser $socialUser,$rawSocialUser,$provider){
        $socialUser2=$this->extractSocialUser($rawSocialUser,$provider);
        return $this->updateToken($socialUser,$socialUser2);
    }

    /**
     * Checks if the user token from provider has expired for a given social user
     *
     * @param SocialUser $socialUser
     * @return boolean True if the token has expired
     */
    public function tokenExpired(SocialUser $socialUser){
        // return false; //TODO: remove
        // $expireTime=Carbon::now()->addMinutes($this->socialTokenExpire);
        // if($socialUser->social_token_at->gt($expireTime)){
        //     return true;
        // }
        // return false;
        //dd($socialUser->social_token_expires_in);
        $expireTime1= $socialUser->updated_at->addSeconds($this->socialTokenExpire);// expire time from admin
        $expireTime2= $socialUser->updated_at->addSeconds($socialUser->social_token_expires_in);// expire time from provider
        $now=Carbon::now();

        if($now->gt($expireTime1) or $now->gt($expireTime2)) //Whcich ever expires first
        {
            return true;
        }
        return false;
        
    }

    /**
     * Sets the call to provider. Should be use to set/save the call type and provider when making a call to provider. Unsets the call types when called with no or null arguments
     *
     * @param string $callType Any call types in the  list of providerCallTypes
     * @param string $provider Any provider in the list of providers
     * @return void
     */
    public function setProviderCall($callType=null,$provider=null){

        if(!$callType and !$provider){
            Session::forget('social_user_provider_call');
            return 1;
        }
        if(!in_array($callType,$this->providerCallTypes)){
            return -1;
        }
        if(!in_array($provider,$this->providers)){
            return -2;
        }

        Session::put('social_user_provider_call',$provider.':'.$callType);

        return 1;
        
    }

    /**
     * Gets the set call type
     *
     * @return mixed Array when successfull Array[0]=callType; Array[1]=provider. False when it cannot find such setting
     */
    public function getProviderCall(){
        //Session::put('social_user_provider_call','ww');
        $call=Session::get('social_user_provider_call');
        //dd($call);
        if($call){
            return explode(':',$call);
        }else{
            return false;
        }
    }

    

    /**
     * Clears the provider call information
     * 
     * @return @see setproviderCall
     */
    public function clearProviderCall(){
        return $this->setProviderCall();
    }

    /**
     * Gets and clear the set provider call
     *
     * @return @see getProviderCall()
     */
    public function getAndClearProviderCall(){
        $call=$this->getProviderCall();
        $this->clearProviderCall();
        return $call;
    }
    
    

    // /**
    //  * Checkes if a given social user token has expired
    //  *
    //  * @param SocialUser $socialUser
    //  * @return boolean true if the token has expired
    //  */
    // function tokenExpired(SocialUser $socialUser){
    //     return ($authSocialuser->social_token_expires_in>333333333333333333333333333333333333);//TODO compare properly 
    // }

    /**
     * COmpare twho social users to see if they are Strictly the same. The most make the same main values to be equal
     *
     * @param SocialUser $socialUser1
     * @param SocialUser $socialUser2
     * @return boolean True if the are the same
     */
    public function areEqualEqual(SocialUser $socialUser1,SocialUser $socialUser2){
        // $table->unsignedBigInteger('user_id');
        // $table->string('provider')->nullable();
        // $table->string('social_name')->nullable();
        // $table->string('social_email')->nullable();
        // $table->string('social_id')->unique();
        // $table->string('social_nickname')->nullable();
        // $table->string('social_avatar')->nullable();
        // $table->string('social_token')->nullable();
        // $table->string('social_token_refresh')->nullable();
        // $table->string('social_token_expires_in')->nullable();
        // $table->string('social_token_secret')->nullable();
        // $table->unsignedTinyInteger('login_enabled')->default(1);
        // $table->tinyInteger('status')->default(1);
        if(
            !$socialUser1->is($socialUser2)
            and
            $socialUser1->user_id!=$socialUser2->user_id
            and
            !strcmp($socialUser1->social_name,$socialUser2->social_name)
            and
            !strcmp($socialUser1->social_email,$socialUser2->social_email)
            and
            !strcmp($socialUser1->social_id,$socialUser2->social_id)
            and
            !strcmp($socialUser1->social_nickname,$socialUser2->social_nickname)
            and
            !strcmp($socialUser1->social_avatar,$socialUser2->social_avatar)
            and
            !strcmp($socialUser1->social_token,$socialUser2->social_token)
            and
            !strcmp($socialUser1->social_token_refresh,$socialUser2->social_token_refresh)
            and
            !strcmp($socialUser1->social_token_expires_in,$socialUser2->social_token_expires_in)
            and
            !strcmp($socialUser1->social_token_secret,$socialUser2->social_token_secret)
            and
            $socialUser1->social_login_enabled==$socialUser2->login_enabled
            and
            $socialUser1->social_status==$socialUser2->status

        )
        return true;
        return false;
    }

    /**
     * Update first input with the second. Only details from the provider is updated although the user id is not
     *
     * @param SocialUser $socialUser1
     * @param SocialUser $socialUser2
     * @return boolean True on success
     */
    public function update(SocialUser $socialUser1,SocialUser $socialUser2){
        if($socialUser2->social_name)
            $socialUser1->social_name=$socialUser2->social_name;
        
        if($socialUser2->social_email){
            //Only go ahead if the email does not already exist
            $c1=User::where('email',$socialUser2->social_email)->count();
            $c2=SocialUser::where('social_email',$socialUser2->social_email)->count();
            if(!($c1+$c2)){
                $socialUser1->social_email=$socialUser2->social_email;
            }
        }
        
        if($socialUser2->social_id)
            $socialUser1->social_id=$socialUser2->social_id;
        
        if($socialUser2->social_nickname)
            $socialUser1->social_nickname=$socialUser2->social_nickname;
        
        if($socialUser2->social_avatar)
            $socialUser1->social_avatar=$socialUser2->social_avatar;
        
        if($socialUser2->social_token)
            $socialUser1->social_token=$socialUser2->social_token;
        
        if($socialUser2->social_token_refresh)
            $socialUser1->social_token_refresh=$socialUser2->social_token_refresh;
        
        if($socialUser2->social_token_expires_in)
            $socialUser1->social_token_expires_in=$socialUser2->social_token_expires_in;
        
        if($socialUser2->social_token_secret)
            $socialUser1->social_token_secret=$socialUser2->social_token_secret;
            
        return $socialUser1->save();
        
        
    }

    /**
     * Update first input  with the raw data from provider
     *
     * @param SocialUser $socialUser
     * @param array $rawSocialUser
     * @param string $provider
     * @return @see update
     */
    public function updateWithRaw(SocialUser $socialUser,$rawSocialUser,$provider){
        $socialUser2=$this->extractSocialUser($rawSocialUser,$provider);
        return $this->update($socialUser,$socialUser2);

    }

     /**
     * Update the social user that matches the supplied raw social user with it
     *
     * @param object $rawOrSocialUser Raw or an instance of social user that will be used to search database
     * @param array $provider
     * @param User $user
     * @return @see update
     */
    public function updateMatchWithRaw($rawOrSocialUser,$provider,User $user){
        $socialUser2=false;
        $cls=SocialUser::Class;
        if(!($rawOrSocialUser instanceof $cls)){
            
            $socialUser2=$this->extractSocialUser($rawOrSocialUser,$provider);
        }
        $socialUser1=$this->getMatchByUser($rawOrSocialUser,$provider,$user);
        //$socialUser2=$this->extractSocialUser($rawSocialUser,$provider);
        return $this->update($socialUser1,$socialUser2);

    }

    /**
     * Removes session data data set 
     *
     * @return void
     */
    public static function clearSession(){
        Session::forget('auth_social_user_id');
        Session::forget('social_user_provider_call');
    }

    /**
     * MAtch a social user with the specified parameters
     *
     * @param mixed $socialUser Raw (e.g user object of Sociallite) or instance of SocialUser
     * @param string $provider
     * @param User $user
     * @return mixed SocialUser or null
     */
    public function getMatchByUser($socialUser,$provider,User $user){
        $cls=SocialUser::Class;
        if(!($socialUser instanceof $cls)){
            
            $socialUser=$this->extractSocialUser($socialUser,$provider);
        }
        
        return $user->socialUsers()->where('provider',$socialUser->provider)->where('social_id',$socialUser->social_id)->first();
        
    }

    /**
     * MAtch a social user with the specified parameters.
     *
     * @param mixed $socialUser Raw (e.g user object of Sociallite) or instance of SocialUser
     * @param string $provider =null
     * @return mixed SocialUser or null
     */
    public function getMatch($socialUser,$provider=null){
        $cls=SocialUser::Class;
        if(!($socialUser instanceof $cls)){
            
            $socialUser=$this->extractSocialUser($socialUser,$provider);
        }
        return SocialUser::where('provider',$socialUser->provider)->where('social_id',$socialUser->social_id)->first();// NOTE: no need to check email because the email might change at the provider's end. //where('social_email',$socialUser->social_email)->
        
    }

    /**
     * MAtch a social user with the specified parameters. Note That this can match any provider
     *
     * @param mixed $socialUser Raw (e.g user object of Sociallite) or instance of SocialUser
     * @param string $provider =null
     * @return mixed SocialUser or null
     */
    public function getMatchByOnlySocialEmail($socialUser,$provider=null){
        $cls=SocialUser::Class;
        if(!($socialUser instanceof $cls)){
            
            $socialUser=$this->extractSocialUser($socialUser,$provider);
        }
        return SocialUser::where('social_email',$socialUser->social_email)->first();
        
    }

    // /**
    //  * MAtch a social user with the specified parameters. Note that this can match any user
    //  *
    //  * @param mixed $socialUser Raw (e.g user object of Sociallite) or instance of SocialUser
    //  * @param string $provider =null
    //  * @return mixed SocialUser or null
    //  */
    // public function getMatchByOnlyUsersEmail($socialUser,$provider=null){
    //     $cls=SocialUser::Class;
    //     if(!($socialUser instanceof $cls)){
            
    //         $socialUser=$this->extractSocialUser($socialUser,$provider);
    //     }
    //     return User::where('email', $socialUser->social_email)->first();
        
    // }

     /**
     * MAtch a social user with the specified parameters.
     *
     * @param mixed $socialUser Raw (e.g user object of Sociallite) or instance of SocialUser
     * @param string $provider =null
     * @return mixed SocialUser or null
     */
    public function getMatchByAny($socialUser,$provider=null){
        $su=$this->getMatch($socialUser,$provider);
        if($su) return $su;

        $su=$this->getMatchByOnlySocialEmail($socialUser,$provider);
        if($su) return $su;

        $su=$this->getMatchByOnlyUsersEmail($socialUser,$provider);
        if($su) return $su;

        return null;   
    }
    

}
