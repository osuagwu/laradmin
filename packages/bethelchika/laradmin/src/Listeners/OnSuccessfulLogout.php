<?php

namespace BethelChika\Laradmin\Listeners;

use BethelChika\Laradmin\User;
use Illuminate\Auth\Events\Logout;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use BethelChika\Laradmin\Social\SocialUserManager;

class OnSuccessfulLogout
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
     * @param  Logout  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        // Clear uneeded session data set by various classes
        User::clearSession();
        SocialUserManager::clearSession();
    }
}
