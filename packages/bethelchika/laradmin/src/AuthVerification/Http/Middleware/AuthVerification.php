<?php

namespace BethelChika\Laradmin\AuthVerification\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use BethelChika\Laradmin\LoginAttempt;
use BethelChika\Laradmin\AuthVerification\AuthVerificationManager;




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

      // So first we only bother if user is logged in 
      if($user){
        // Then we check if we need to examine an attempt for verification
        if (AuthVerificationManager::shouldCheckVerification()) { 
            
            //We will then go ahead to examine an attempt for verification
            $current_attempt=LoginAttempt::getCurrentAttempt($request);
             
            if (AuthVerificationManager::has2Verify($current_attempt)) {
                // We need to verify but we need to make sure that we are not 
                // currently on a special route, e.g to prevent redirection loop, etc.
                $excepts=array_merge(
                    config('laradmin.auth_verification_except_path', []),
                    [route('user-auth-v', [], false),route('logout', [], false)],
                    [route('send-email-confirmation', [], false)]
                );

                for ($i=0;$i<count($excepts);$i++) {
                    $excepts[$i]=trim($excepts[$i], '/');
                }

                if (!Str::startsWith(trim($request->path(), '/'), $excepts)) {//Prevent redirection loop
                    // Then we will send the user to go and verify
                    return redirect()->route('user-auth-v');
                }
                
            }else{
                // We were asked to check if verification was required, but verification 
                // was actually not required when we checked. So lets make sure that session
                // data, which may have been the reason for the check, is up to date. 
                if(AuthVerificationManager::hasJustLoggedIn()){
                  AuthVerificationManager::resetSession();
                }
                
            }
        }
      }

      

      // if($user){
      //   $current_attempt=LoginAttempt::getCurrentAttempt($request); 
      //   if(!$current_attempt->is_verified or $current_attempt->reverify or $current_attempt->user_id!=$user->id) {//NOTE: the check '$current_attempt->user_id!=$user->id' is very unneccessary as the 'user_id' and 'id' will always refer to the same user from $request instance.
      //       $excepts=array_merge(
      //         config('laradmin.auth_verification_except_path', []),
      //         [route('user-auth-v', [], false),route('logout', [], false)],
      //         [route('send-email-confirmation', [], false)]
      //     );

      //     for ($i=0;$i<count($excepts);$i++) {
      //         $excepts[$i]=trim($excepts[$i], '/');
      //     }

      //     if (!Str::startsWith(trim($request->path(), '/'), $excepts)) {//Prevent redirection loop
      //         return redirect()->route('user-auth-v');
      //     }
      //   }
      // }
      return $next($request);
      
        
    }
}
 