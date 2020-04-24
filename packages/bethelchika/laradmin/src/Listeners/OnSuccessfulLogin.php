<?php

namespace BethelChika\Laradmin\Listeners;

use BethelChika\Laradmin\AuthVerification\AuthVerificationManager;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OnSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        // We first make sure that the user object is uptodate
        $user=$event->user->fresh();

        

        // Update last login
        $user->loginAt();
        
        
        $is_remember_me=Auth::viaRemember();

        // Set login attempt in verification:: 
        // Must be don after we have called $user->loginAt() as 
        // AuthVerification may need to check last login which needs 
        // to be up to date.
        AuthVerificationManager::onLogin($is_remember_me);


        // Check if there is restriction and apply it
        $user->applyLoginRestrictions();

        // Log login
        $attempt=$user->logSuccessfulLogin();

        if(!$is_remember_me){//TODO: But after a while of login in via remember me we should one day force reverification. Perhaps we could count all remember me logins and once a thresh is reached we then force $attempt->mustReverify()
            $attempt->checkXfactor();
        }
            

        

        // Check if user is self-deactivated and cancel it
        $user->autoReactivate();
        
        // Verify
        //AuthVerificationManager::checkSuccessfulLogin($user,$attempt);
            
        
    }
}
