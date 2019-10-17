<?php

namespace BethelChika\Laradmin\AuthVerification\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use BethelChika\Laradmin\LoginAttempt;
use Illuminate\Support\Facades\Session;


class AuthVerification
{
    

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      $user=$request->user();
      //if($user and $user->authVerification()->count()){
      if($user){
        $current_attempt=LoginAttempt::getCurrentAttempt($request); 
        if(!$current_attempt->is_verified or $current_attempt->reverify or $current_attempt->user_id!=$user->id) {//NOTE: the check '$current_attempt->user_id!=$user->id' is very unneccessary as the 'user_id' and 'id' will always refer to the same user from $request instance.
            $excepts=array_merge(
              config('laradmin.auth_verification_except_path', []),
              [route('user-auth-v', [], false),route('logout', [], false)],
              [route('send-email-confirmation', [], false)]
          );

          for ($i=0;$i<count($excepts);$i++) {
              $excepts[$i]=trim($excepts[$i], '/');
          }

          if (!Str::startsWith(trim($request->path(), '/'), $excepts)) {//Prevent redirection loop
              return redirect()->route('user-auth-v');
          }
        }
      }
      return $next($request);
      
        
    }
}
 