<?php

namespace BethelChika\Laradmin\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;
//use BethelChika\Laradmin\Traits\AuthManagement;


class CheckReAuthentication
{
    //use AuthManagement;

    /**
     * We define the reauth status code as 420.
     */
    const STATUS_CODE=420;

    /**
     * Time in minutes since last auth or re auth at which reauth is required
     *
     * @var integer
     */
    private $TIME_OUT=60;  //TODO: move to and read from settings
    /**
     * Handle an incoming request.
     * CAUTION: One can bypass the short $timeOut by first visiting a route with a 
     * longer timeout. This is possible because the time,re_auth_at, is reset as 
     * long as re-auth time is not reached (but also after re-authentication). 
     * So calls for short $timeOut is at the 
     * mercy of calls at longer $timeOut. So ultimately the most reliable 
     * $timeOut is the longest one in the entire app. The best solution is 
     * to keep $timeOut fixed for all calls to this middleware.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param int $timeOut=$TIME_OUT The time in minutes since auth/re-auth after which reauth is enforced.
     * @return mixed
     */
    public function handle($request, Closure $next,$timeOut=null)
    {

        if($timeOut){
            $minutes=$timeOut;
        }else{
            $minutes=$this->TIME_OUT;
        }
        $now=Carbon::now();
        $lastTime=Session::get('re_auth_at',$now->subYears(5)->timestamp);
        $lastTime=Carbon::createFromTimestamp($lastTime);
        
        //dd($lastTime->diffForHumans(Carbon::now()));
        if($lastTime->addMinutes($minutes)->gt(Carbon::now())){
            Session::put('re_auth_at',Carbon::now()->timestamp);//This line allows resetting the re_auth_at so that no need to reauth as long as the user keeps requesting pages that needs reauth 
            Session::put('re_auth_on',0);// Others scripts can use this to tell that reauth is not currently needed
            return $next($request);
        }
        else{
            Session::put('re_auth_url_intended',URL::full());
            Session::put('re_auth_on',1);// Other scripts can use this to tell if user has been told to reauth
           if($request->ajax() or $request->wantsJson()) { 
               
               $data=['message'=>'Re authentication is required', 
                        'info'=>'re-auth-url:'.route('re-auth'),

                        // Build errors to look like Laravel's validation errors for ajax.
                        'errors'=>[
                            're-auth'=>['Please re-authenticate',],
                        ],
                    ];

                return response()->json($data,self::STATUS_CODE,[],JSON_UNESCAPED_SLASHES);
           } 
           return redirect()->route('re-auth',[]);
        }
        
    }
}
