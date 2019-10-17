<?php

namespace BethelChika\Laradmin\AuthVerification;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use BethelChika\Laradmin\User;
use Illuminate\Support\Facades\DB;
use BethelChika\Laradmin\LoginAttempt;
use BethelChika\Laradmin\AuthVerification\Models\AuthVerification;

class AuthVerificationManager
{
    

    /**
     * Verification channels
     * The key to the outer array is that unique tag of each channel. 
     * Each channel sets ths max risk level it can handle and defines its class.
     * TODO: make it possible to rigister channels dynamically, by creating a register method. This will make it possible for external scripts to define new channels.
     *
     * @var array
     */
    private static $channels = [
        'email' => ['max_level' => 10, 'class' => \BethelChika\Laradmin\AuthVerification\Channels\Email::class],
        'security_question' => ['max_level' => 10, 'class' => \BethelChika\Laradmin\AuthVerification\Channels\SecurityQuestion::class],
        'password' => ['max_level' => 2, 'class' => \BethelChika\Laradmin\AuthVerification\Channels\Password::class],
    ];


    /**
     * Allows dynamic registration of channels
     *
     * @param string $tag A unique tag for the channel
     * @param integer $max_level The max security level that this should handle
     * @param string $class The fully qualified classname of the channel
     * @return void
     */
    public static function registerChannel($tag,$max_level,$class){
        self::$channels[$tag]=['max_level'=>$max_level,'class'=>$class];
    }



    /**
     * Checks if we need to set a verification following a successful login
     * TODO: This function is not in use. But the pieces of ideas inside it may be used to determin security level
     * @param User $user
     * @param LoginAttempt $attempt
     * @return boolean
     */
    public static function checkSuccessfulLogin(User $user, LoginAttempt $attempt)
    {


        // Set default risk level
        $levels[] = 0;

        /*______________________________________________________________________________________ 
        Is there attempt info? 
        ________________________________________________________________________________________
        */
        if (!$attempt) {
            $levels[] = 9;
        } 
        elseif(!$attempt->is_verified){
            $levels[] = 9;
        }
        else {
            $attempt = $attempt->refresh(); // Just to make sure it is upto date

            /*______________________________________________________________________________________
            Is this a unique successful attempt or is this attempt not verified?
            ________________________________________________________________________________________
            */
            if ($attempt->count == 1) {

                // Count the number of successful and Unsuccessful login by this user from the current plateform
                // $platform_attempts=$user->loginAttempts()
                // ->select(DB::raw('count(*) as n,is_success'))->groupBy('is_success')
                // ->where('platform',$attempt->platform)->where('platform_version',$attempt->platform_version)
                // ->get()->groupBy('is_success');

                // Metrics para the current plateform
                $wheres = [
                    ['user_id', '=', $user->id],
                    ['platform', '=', $attempt->platform],
                    ['platform_version', '=', $attempt->platform_version]
                ];
                $platform_metrics = LoginAttempt::attemptsMetrics($wheres);

                // Metric for current device and browser
                $wheres = [
                    ['user_id', '=', $user->id],
                    ['mobile_device', '=', $attempt->mobile_device],
                    ['device_type', '=', $attempt->device_type],
                    ['browser', '=', $attempt->browser],
                    ['browser_version', '=', $attempt->browser_version],
                ];
                $device_agent_metrics = LoginAttempt::attemptsMetrics($wheres);

                // Matrics para the current location
                $wheres = [
                    ['user_id', '=', $user->id],
                    ['city', '=', $attempt->city],
                    ['country', '=', $attempt->country]
                ];
                $location_metrics = LoginAttempt::attemptsMetrics($wheres);



                // Compute index
                $failed_index = $platform_metrics['failed_count'] + $device_agent_metrics['failed_count'] + 5 * $location_metrics['failed_count'];
                $success_index = $platform_metrics['success_count'] + $device_agent_metrics['success_count'] + 5 * $location_metrics['success_count'];
                $index = $success_index - $failed_index;

                //Decision
                if ($index < 0) {
                    $levels[] = 8;
                }
            }





            /*______________________________________________________________________________________
            Does the guest user have many attemps expecially at high rate from this location
            ________________________________________________________________________________________
            */

            // Matrics para the current location
            $wheres = [
                ['user_id', '=', $user->getGuestId()],
                ['city', '=', $attempt->city],
                ['country', '=', $attempt->country],
                ['updated_at', '>', Carbon::yesterday()],
            ];
            $guest_location_metrics = LoginAttempt::attemptsMetrics($wheres);

            // Note that a guest never have a successful attempt
            if (
                $guest_location_metrics['failed_count'] > 7
                or $guest_location_metrics['avg_failed_rate'] > 0.5
                or $guest_location_metrics['max_failed_rate'] > 0.8
            ) {

                $levels[] = 6;
            }
        }


        /*______________________________________________________________________________________
            Return
            ________________________________________________________________________________________
            */
        if (max($levels) > 0) {
            return $attempt->mustReverify(max($levels));
        } else {
            return false;
        }
    }








    /**
     * Return all channels or those with max_levels >= the given $min_level
     * @param string $min_level Only channels with max_levels >= $min_level will be returned
     * @return \BethelChika\Laradmin\AuthVerification\Contracts\Channel[]
     */
    public static function getChannels($min_level=null)
    {
        if($min_level){
            $channels= collect(self::$channels)
                    ->where('max_level','>=',$min_level)
                    ->all();

        }else{
            $channels= self::$channels;
        }
        

        $CHANS=[];
        foreach($channels as $channel ){
            $CHANS[]=new $channel['class'];
        }

        return $CHANS;
    }

    /**
     * Return a channel with a given tag
     *
     * @param string $tag
     * @return \BethelChika\Laradmin\AuthVerification\Contracts\Channel
     */
    public static function getChannel($tag)
    {
        $channel=self::$channels[$tag] ?? null;
        if($channel){
            return new $channel['class'];
        }
        return null;
    }

    /**
     * Return a channels max level
     *
     * @param string $tag
     * @return int
     */
    public static function getChannelMaxLevel($tag)
    {
        return self::$channels[$tag]['max_level'] ?? null;
        
    }
 
}
