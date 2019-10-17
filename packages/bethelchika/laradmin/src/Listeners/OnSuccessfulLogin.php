<?php

namespace BethelChika\Laradmin\Listeners;

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

        

        // Check if there is restriction and apply it
        $user->applyLoginRestrictions();

        // Log login
        $attempt=$user->logSuccessfulLogin();

        if(!Auth::viaRemember()){
            $attempt->checkXfactor();
        }
            

        

        // Check if user is self-deactivated and cancel it
        $user->autoReactivate();
        
        // Verify
        //AuthVerificationManager::checkSuccessfulLogin($user,$attempt);
            
        
    }
}
