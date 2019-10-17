<?php

namespace BethelChika\Laradmin;
use Carbon\Carbon;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use BethelChika\Laradmin\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use BethelChika\Laradmin\Tools\Tools;
use Illuminate\Database\Eloquent\Model;
use GeoIp2\Exception\AddressNotFoundException;
use BethelChika\Laradmin\AuthVerification\Contracts\Channel;
use BethelChika\Laradmin\AuthVerification\AuthVerificationManager;


class LoginAttempt extends Model
{

    /**
     * Used to capture the Auth verification manager so that is not newed up all the time
     *
     * @var AuthVerificationManager
     */
    public static $manager=null;


    
    public function user(){
        return $this->belongsTo('BethelChika\Laradmin\User');
    }

    /**
     * Check if a user need extra factor authentication and apply it
     *
     * @return void
     */
    public function checkXfactor(){
        if ($this->user->hasXfactor()){
            $this->mustReverify(7);
        }
    }

    /**
     * Does this attempt need any verification?
     *
     * @return boolean
     */
    public function has2Verify(){
        return !$this->is_verified or $this->reverify;
        
    }

    /**
     * Forces reverification of this attempt at the given security level
     *
     * @param integer $level Security level
     * @return boolean
     */
    public function mustReverify($level=1){
        $this->reverify=$level;
        return $this->save();
    }

    /**
     * Forces verification on all the users previous attempts
     *
     * @param User $user
     * @return void
     */
    public static function mustReverifyAll(User $user,$level=1){
        foreach($user->loginAttempts as $attempt){
            $attempt->mustReverify($level);
        }
        
    }




    /**
     * Get the attempt for the current given request. If the attempt has been used to login before 
     * then a previously saved version will be returned.
     *
     * @param Request $request
     * @return LoginAttempt
     */
    public static function getCurrentAttempt(Request $request){
        $current_attempt=self::extractAttemptFromRequest($request);
        $attempt_prev=$current_attempt->getMatchedAttempt(false);
        return $attempt_prev?:$current_attempt;
        
    }

    /**
     * Log an attempt when a user is registered.
     *
     * @param User $user
     * @param Agent $agent
     * @param string $ip
     * @return LoginAttempt
     */
    public static function extractAndLogRegisteredAttempt(User $user,Agent $agent,$ip){
        $login_attempt=self::exctractAttempt($user, true, $agent, $ip);
        
        // The attempt used to make a registration is considered verified.
        $login_attempt->is_verified=1;

        return $login_attempt->logAttempt(false);
    }

    /**
     * Extracts and Logs a login attempt
     * @param @see self::extractAttempt();
     * @return @see logAttempt()
     */
    public static function extractAndLogAttempt(User $user,$is_success=false,Agent $agent,$ip,$credentials=[]){       
        $login_attempt=self::exctractAttempt($user, $is_success, $agent, $ip, $credentials);
        return $login_attempt->logOrUpdateAttempt();
    }


    /**
     * Extract the login attempt from request.
     *
     * @param Request $request
     * @param boolean $is_success @see self::extractAttempt();
     * @param array $credentials @see self::extractAttempt();
     * @return LoginAttempt
     */
    public static function extractAttemptFromRequest(Request $request,$is_success=false,$credentials=[]){
        return self::exctractAttempt($request->user(), $is_success, new Agent(), $request->ip(), $credentials);
    }

    /**
     * Updates an attempt if it exists, or make a new log otherwise
     *
     * @return LoginAttempt
     */
    public function logOrUpdateAttempt(){
        // What is the fastest possible rate in attemps/seconds. This is useful when
        // there is no difference in time b/w the current and the last attempt. 
        // Attempts reaching this rate should be considered dangerously fast.
        // Note: After changing this value, make sure that the 'rate' column on the 
        // login_attempts table can acurately store the new max rate.
        // Note: We could move this to config but there's no need as it is not like going to be chnaged.
        $max_rate=1;

        $login_attempt_prev=$this->getMatchedAttempt();
        if ($login_attempt_prev) {
            // So there is a pre attempt, we should instead update;

            // first update some field with the new login
            $login_attempt_prev->credentials=$this->credentials;
            


            // Now update the rate if there is at least one count.(Note that is is totally 
            // possible to have a 0 count==0 e.g during registration)
            if ($login_attempt_prev->counts) {
                $since_last_attempt=$login_attempt_prev->updated_at->diffInSeconds(Carbon::createFromTimestamp(time()), true); //TODO: change diffInSeconds() to floatDiffInSeconds() for better precision after upgrading Carbon
            

                $rate=$max_rate;
                if ($since_last_attempt> 1/$max_rate) {
                    $rate=1/($since_last_attempt);
                }

                // Now we will use the cumulative moving avg to update the rate
                $n=$login_attempt_prev->counts;
                $CMA=$login_attempt_prev->rate;
                $CMA_1=($rate + $n*$CMA)/($n+1); //Cummulative moving average
                $login_attempt_prev->rate=$CMA_1;
            }



            // CAUTION: We are going to increment count but if it gets too big, then we will
            // reset it to avoide exceeding the integer bit limits on database. Of cource this
            // reseting will affect the rate calculation but it is the best we can do here
            // otherwise the counts will get too large.
            $login_attempt_prev->counts+=1;
            if ($login_attempt_prev->counts>4000000000) {
                $login_attempt_prev->counts=4000000000;
            }

            

            $re=$login_attempt_prev->save();

            $this->maintenance();
            return $login_attempt_prev;
        }else{
            return $this->logAttempt();
        }
    }


    /**
     * Logs a login
     * 
     * @return LoginAttempt|null
     */
    public function logAttempt($increment_count=true){
        //try{//
            

            if($increment_count){
                $this->counts+=1;
            }
            
            $re=$this->save();
            $this->maintenance();


            if($re){
                return $this;
            }

        // }
        // catch(\Exception $ex){
        //     Log::error(__CLASS__.':'.__METHOD__.': Unable to log login because; msg=>'.$ex->getMessage());
        //     throw $ex;
        // }

        return null;
    }

    /**
     * Maintain the table e.g: make sure the number of unique attempts are not over the limit
     *
     * @return void
     */
    private function maintenance(){
        
        $max_rows=config('laradmin.login_attempt_max_rows',5);
        $attempt_total=$this->user->loginAttempts()->count();
        $attempt_rem=$attempt_total-$max_rows;
        if($attempt_rem>0){
            $this->user->loginAttempts()->oldest()->limit($attempt_rem)->delete();
        }
    }

    /**
     * Returns an attempt made previously that is similar to this.
     *
     * @param boolean $for_login When true, it is assumed that this match is to be made for login purposes and therefore checks also that fields like is_success matches.
     * @return LoginAttempt|null
     */
    public function getMatchedAttempt($for_login=true){
        //throw new \Exception('This method is incomplete');
        // Now we check if similar attempt has been made before and update instead
        $match_props=['user_id','ip',
            'languages','browser','browser_version',
            'platform','platform_version',
            'mobile_device','device_type','robot'
        ];

        if($for_login){
            $match_props[]='is_success';
        }

        $login_attempt_prev=LoginAttempt::where(function($query)use ($match_props){
            foreach($match_props as $mp){
                $query->where($mp,$this->$mp);
            }
        })->first();
        //dd($login_attempt_prev);
        return $login_attempt_prev;
    }


    /**
     * Extract a login attempt
     * @param User $user
     * @param boolean $is_success True if the login attempt was successful
     * @param Agent $agent
     * @param string $ip The ip address for the login request
     * @param array $credentials The credential  user tried to login with. Array has indexes 'email' and 'password'.
     * @return LoginAttempt The returned model is not saved to database
     */
    public static function exctractAttempt(User $user,$is_success=false,Agent $agent,$ip,$credentials=[]){
        $address=null;
        try {
            $address=Tools::ip2Address($ip);
        }
        catch(AddressNotFoundException | \InvalidArgumentException $ex){
            //dd($ex);
        }
        catch(\Exception $ex){
            
        }

        $login_attempt=new loginAttempt;
        $login_attempt->user_id=$user->id;
        $login_attempt->is_success=$is_success?1:0;

        
        
        // the username/email is correct if the $user!==GUEST. 

        $login_attempt->ip=$ip;
        if ($address) {
            $login_attempt->ip=$ip;
            $login_attempt->latitude=$address['latitude'];
            $login_attempt->longitude=$address['longitude'];
            $login_attempt->city=$address['city_name'];
            $login_attempt->country=$address['country_name'];
        }

        //Agent stuff
        $login_attempt->languages =serialize($agent->languages());

        $login_attempt->browser=$agent->browser();
        $login_attempt->browser_version=$agent->version($agent->browser());

        $login_attempt->platform=$agent->platform();
        $login_attempt->platform_version=$agent->version($agent->platform());

        $login_attempt->mobile_device=$agent->device();
        
        $login_attempt->robot=$agent->robot();


        
        if($agent->isPhone()){
            $login_attempt->device_type='phone';
        }
        elseif($agent->isTablet()){
            $login_attempt->device_type='tablet';
        }
        elseif($agent->isDesktop()){
            $login_attempt->device_type='desktop';
        }

            // Now update credentials
            // Perhaps we don't need to but to make sure that we are not storing any plain  
        // text password, will delete the password from the credentials.
        if (count($credentials)) {
            $credentials['password']='';
            $login_attempt->credentials=json_encode($credentials);
        }

        return $login_attempt;
    }
  
    //     /**
    //  * Count a specific number of attemps
    //  * @param array $wheres, Array of 3 element array of where clause e.g: [['user_id','=',3],['device','=','phone']]
    //  * @return Array Of two elements where [0=>fails,1=>successes]
    //  */
    // public static function countAttempts($wheres){
    //     // Count the number of successful and Unsuccessful login by this user from the current plateform
    //     $attempts=LoginAttempt::select(DB::raw('SUM(counts) as n,is_success'))
    //     ->groupBy('is_success');
    //     foreach($wheres as $where){
    //         $attempts->where($where[0],$where[1],$where[2]);
    //     }
    //     $attempt_groups=$attempts->get()->groupBy('is_success');
    //     $failed_count=$attempt_groups[0][0]->n; // Unsuccessful login count
    //     $success_count=$attempt_groups[1][0]->n;// Suceessful loging count

    //     return [$failed_count,$success_count];
    // }


    /**
     * Return matrics of attempts
     * TODO: UNUSED: Use this method when calculating security level
     * @param array $wheres, Array of 3 element array of where clause e.g: [['user_id','=',3],['device','=','phone']]
     * @return Array  e.g: [
     *                          "failed_count" => "8",
     *                          "success_count" => 0,
     *                          "max_failed_rate" => 0.0,
     *                          "avg_failed_rate" => 0.0,
     *                          "max_success_rate" => 0,
     *                          "avg_success_rate" => 0,
     *              ]
     */
    public static function attemptsMetrics($wheres=[]){
        // Count the number of successful and Unsuccessful login by this user from the current plateform
        $attempts=LoginAttempt::select(DB::raw('SUM(counts) as n,MAX(rate) as max_rate,AVG(rate) as avg_rate,is_success'))
        ->groupBy('is_success');
        foreach($wheres as $where){
            $attempts->where($where[0],$where[1],$where[2]);
        }
        $attempt_groups=$attempts->get()->groupBy('is_success');


        

        //Note that is_sucess used for grouping can only have values {0=>failed,1=>success} hence index $attempt_groups[0] and $attempt_groups[1]
        $failed_count=$attempt_groups[0][0]->n??0; // Unsuccessful login count
        $success_count=$attempt_groups[1][0]->n??0;// Successful loging count
        
        $max_failed_rate=$attempt_groups[0][0]->max_rate??0;// 
        $avg_failed_rate=$attempt_groups[0][0]->avg_rate??0;// 
        
        $max_success_rate=$attempt_groups[1][0]->max_rate??0;// 
        $avg_success_rate=$attempt_groups[1][0]->avg_rate??0;// 

        $metrics= ['failed_count'=>$failed_count,
                'success_count'=>$success_count,
                'max_failed_rate'=>$max_failed_rate,
                'avg_failed_rate'=>$avg_failed_rate,
                'max_success_rate'=>$max_success_rate,
                'avg_success_rate'=>$avg_success_rate
        ];

        
        return $metrics;
    }

    /**
     * Gets the security level
     *  TODO: This function may need to be improved to take into account, rate, tries, location/ip etc
     * @return int
     */
    public function getLevel(){
        $level=0;

        $counts=$this->counts??0;// We use ?? in case the attempt hasn't been saved before
        $is_verified=$this->is_verified??0; // We use ?? in case the attempt hasn't been saved before

       

        if(!$is_verified){
            $level+=7;
        }elseif($counts>5){
            $level+=2;
        }

        if($this->reverify){
            $level=max([$level,$this->reverify]);
        }

        return $level;
    }

    /**
     * Return the channels suitable for this verification
     *
     * @return array
     */
    public function getChannels(){
        return self::getManager()->getChannels($this->getLevel());
    }

 

    /**
     * Can the given channel be used to verify this auth verification.
     *
     * @param Channel $channel
     * @return boolean
     */
    public function canVerify(Channel $channel){
        return $this->canVerifyByTag($channel->getTag());        
    }

    /**
     * Can the given channel, spefied by tag, be used to verify thi auth verification.
     *
     * @param string $tag Channel unique tag
     * @return boolean
     */
    public function canVerifyByTag($tag){
        return self::getManager()->getChannelMaxLevel($tag)>=$this->getLevel();
        
    }

    /**
     * Make verification by deleting row
     *
     * @param Channel $channel
     * @return boolean
     */
    public function verify(Channel $channel){
        if($this->canVerify($channel)){
            $this->is_verified=1;
            $this->reverify=0;
            $this->is_success=1;
            $this->tries=0;
            return $this->save();
        }

        //$this->tried();

        return false;
    }

    /**
     * Increment the number of tries.
     *
     * @return self
     */
    public function tried(){

        $this->tries+=1;
        return $this;

    }

    /**
     * Gets the Auth verification manager object
     *
     * @return void
     */
    public static function getManager(){
        if(!self::$manager){
            self::$manager=new AuthVerificationManager;
        }
        return self::$manager;
    }


}
