<?php
namespace BethelChika\Laradmin\Traits;

use Carbon\Carbon;
use Jenssegers\Agent\Agent;
use BethelChika\Laradmin\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use BethelChika\Laradmin\Tools\Tools;
use BethelChika\Laradmin\LoginAttempt;
use Illuminate\Support\Facades\Session;
use BethelChika\Laradmin\Social\Models\SocialUser;
use BethelChika\Laradmin\Social\SocialUserManager;


trait AuthManagement
{
    /**
     * Controls if a restricted user should be forced to log out. This serves as a default value incase it is not provided in settings
     *
     * @var boolean
     */
    private $LOG_OUT_RESTRICTED_USER=true;
    /**
    * Check if a user has login restrictions
    *
    * @param  void
    * @return boolean
    */
    function hasLoginRestrictions(){
        if ($this->is_active==0){
            return true;
        }else{
            return false;
        }
    }
    /**
     * Logs a Password reset attempt
     *
     * @return LoginAttempt
     */
    public function logPasswordReset(){
        return LoginAttempt::extractAndLogPasswordResetAttempt($this,new Agent(),request()->ip());
    }

   /**
     * Logs a registered user attempt
     *
     * @return LoginAttempt
     */
    public function logRegistered(){
        return LoginAttempt::extractAndLogRegisteredAttempt($this,new Agent(),request()->ip());
    }


    /**
     * Logs a successful login
     *
     * @return LoginAttempt
     */
    public function logSuccessfulLogin(){
        return LoginAttempt::extractAndLogAttempt($this,true,new Agent(),request()->ip());
    }


  /**
   * Logs an unsuccessful login
   *
   * @param User $user
   * @param array $credentials The credential  user tried to login with. Array has indexes 'email' and 'password'
   * @return LoginAttempt
   */
    public static function logFailedLogin(User $user=null,$credentials=null){
        
        if(!$user){
            $user=User::getGuestUser();   
        }
        return LoginAttempt::extractAndLogAttempt($user,false,new Agent(),request()->ip(),$credentials);
    }


    /**
     * Checks if extra factor authentication is enabled
     *
     * @return boolean
     */
    public function hasXfactor(){
        return !!$this->xfactor;
    }


    /**
    * Restrict authentication by forcing a user to log out (depends on settings) if the user is logged in.
    *
    * @param  void
    * @return mixed
    */
    function authRestrict(){
        $logout=config('laradmin.log_out_restricted_user',$this->LOG_OUT_RESTRICTED_USER);
        if(Auth::check()){
            if($logout){
                Auth::logout();
                abort(403,'This account is restricted');
            }else{
                return false;
            }
        }
        return true;
    }


    /**
    * A static method to check if a user has login restrictions
    *
    * @param  User $user
    * @return boolean
    */
    public static function hasAuthRestrictions(User $user){
        if ($user->is_active==0){
            return true;
        }else{
            return false;
        }
    }


      /**
    * Check if a user has login restrictions and restric login if the user has the restriction
    *
    * @param  void
    * @return void
    */
    function applyLoginRestrictions(){
        if($this->hasLoginRestrictions()){
            $this->authRestrict();
        }
    }

          /**
    * Checks if time since users last authentication/reauthentication is more that a given times in minutes. This function is alternative to the middleware that does this duty
    *
    * @param  int $minutes
    * @return boolean False if reauthentication is needed and true otherwise

    static function lastAuthTimeIsLess($minutes){
        $lastTime=Session::get('last_re_auth_at');
        return $lastTime->addMinutes($minutes)<Carbon::now();
    } */


     /**
     * Updates the user login times
     *
     * @return void
     */
    function loginAt(){

        if(Auth::viaRemember()) return; //do not update if user loggend in via 'remember-me'

        $this->last_login_at=$this->current_login_at;
        $this->current_login_at=Carbon::now();
        //$this->last_confirm_auth_at=Carbon::now();
        $this->setReAuthAt();

        $this->save();
    }
    /**
     * Sets the time whe user authenticate or reauthenticated last
     *
     * @param Carbon $dt
     * @return void
     */
    function setReAuthAt(Carbon $dt=null){
        if(Auth::viaRemember()) return; //do not update if user loggend in via 'remember-me'

        if(!$dt){
            $dt=Carbon::now();
        }
        Session::put('re_auth_at',$dt->timestamp);
    }

    /**
     * Return the intended url before reauth was required
     *
     * @return string
     */
    public function getReAuthUrlIntended(){
        return Session::get('re_auth_url_intended','');

    }

    /**
     * Set the intended url before reauth was required. Clears previous value if called with no parameter or empty value.
     *
     * @param string $url The url the user intended to go to before she was required to reauth
     * @return void
     */
    public function setReAuthUrlIntended($url=''){
        if(strlen($url)){
            Session::put('re_auth_url_intended',$url);
        }else{
            Session::forget('re_auth_url_intended');
        }

    }
    /**
     * Reauthenticate user with users password only
     *
     * @param string $password
     * @return boolean true if reauthentication is successful
     */
    function reAuthWithPassword($password){
        return Hash::check($password,$this->password);
    }

    /**
     * Use the specified social user for reauthentication
     *
     * @param SocialUser $authSocialUser The Social user that will be used for the reauth. The ID of this social user is usually stored e.g in session when the used it to login
     * @param SocialUserManager $socialUserManager An instance of this class
     * @param $update =true. This function will update the social user in the database if the freshed one matches
     * @return boolean true when success
     */
    public function reAuthWithSocialUser(SocialUser $authSocialUser,SocialUserManager $socialUserManager,$update=true ){
        //TODO: some of these might need to be moved to social user but I am not sure
        $freshSocialUser=$socialUserManager->refreshSocialUserFromToken($authSocialUser);
        if($freshSocialUser){
            $re=$authSocialUser->social_id==$freshSocialUser->social_id;
            if($re and $update){//Update
                $socialUserManager->update($authSocialUser,$freshSocialUser);
            }
            return $re;
        }
        return false;
    }

     /**
     * Re-authentication by comparing two social users.
     *
     * @param SocialUser $authSocialUser The user used to loging
     * @param SocialUser $freshSocialUser Fresh from provider
     * @return boolean True on success
     */
    public function reAuthWithSocialUsers(SocialUser $authSocialUser,SocialUser $freshSocialUser){
        return ($authSocialUser->social_id==$freshSocialUser->social_id) and ($authSocialUser->provider==$freshSocialUser->provider);//TODO: this comparison should be done in SocialUserManager
    }

    /**
     * Check if the given social user matches any social user account of the spcified user.
     *
     * @param mixed $socialUser. Raw(e.g user object of Sociallite)  or instance of SocialUser
     * @param string $provider The name of the social provider
     * @param User $user
     * @return boolean True if there is a match
     */
    public function reAuthWithMatchSocialUser($socialUser,$provider,User $user,SocialUserManager $socialUserManager){
        if($socialUserManager->getMatchByUser($socialUser,$provider,$user)){
            return true;
        }
        return false;
    }



    /**
     * Increment the number of attempts to reauth by the specified amount
     *
     * @param int $incrementBy
     * @return void
     */
    public function logReAuthAttempt($incrementBy=null){

        if(!$incrementBy){
            $incrementBy=1;
        }
        if (Session::has('re_auth_attemp_count')) {
            $i=intval(Session::get('re_auth_attemp_count'))+$incrementBy;
            //dd(Session::get('re_auth_attemp_count'));
            Session::put('re_auth_attemp_count',$i);
            //Session::forget('re_auth_attemp_count');
        }else{
            Session::put('re_auth_attemp_count',$incrementBy);
        }
    }

    /**
     * Gets the number of reauth attempts
     *
     * @return int The number of reauth attemps
     */
    public function countReAuthAttempt(){
        if (Session::has('re_auth_attemp_count')) {
            //dd(Session::get('re_auth_attemp_count'));
            return intval(Session::get('re_auth_attemp_count'));
        }else{
            return 0;
        }
    }

    /**
     * Resets the number of reauth attempts
     *
     * @return void The number of reauth attemps
     */
    public function resetReAuthAttempt(){
        if (Session::has('re_auth_attemp_count')) {
            return Session::forget('re_auth_attemp_count');
        }
    }


    /**
     * Used to mark the completion of a successful reauthentication
     *
     * @return void
     */
    public function reAuthSuccess(){
        Session::put('re_auth_on',0);
        $this->setReAuthUrlIntended();
        $this->setReAuthAt();
        $this->resetReAuthAttempt();
    }

    /**
     * Used to mark the failed reauthentication attempt after the number of alloed attemp is riched.
     * @param $logoutUser If true the current user will be logged out.
     * @return void
     */
    public function reAuthFail($logoutUser=false){
        $this->resetReAuthAttempt();
        $this->setReAuthUrlIntended();
        if($logoutUser)Auth::logout();
    }

    /**
     * Determine if reauth is currently needed
     *
     * @return boolean
     */
    public function isReAuthOn(){
        $r=Session::get('re_auth_on',0);
        if($r){
            return true;
        }
        return false;
    }

    /**
     * Removes session data set by this class
     *
     * @return void
     */
    public static function clearSession(){
        Session::forget('re_auth_url_intended');
        Session::forget('re_auth_attemp_count');
        Session::forget('re_auth_at');
        Session::forget('re_auth_on');
    }

}
