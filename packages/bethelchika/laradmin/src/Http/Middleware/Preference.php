<?php

namespace BethelChika\Laradmin\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;



class Preference
{
    /** 
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        


        if($user=$request->user()){
            // Set the local
            if($local=$user->getLocal()){
                app()->setLocale($local);
            }

            // Set time zone for display only
            //if(!session()->has('timezone') ){
                if($tz=$user->timezone){
                    session()->put('timezone',$tz);  
                }
                 
           // }
              
        }

        
        // Check f timezone for display is still not set
        if(!session()->has('timezone')){
            //TODO: If time zone is not set, detect it from browser
        }

        return $next($request);
        
    }
}
