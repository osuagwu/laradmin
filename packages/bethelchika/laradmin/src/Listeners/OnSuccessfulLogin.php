<?php

namespace BethelChika\Laradmin\Listeners;

use Illuminate\Auth\Events\Login;
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
        // Update last login
        $event->user->loginAt();

        // Check if there is restriction and apply it
        $event->user->applyLoginRestrictions();

        // Check if user is self-deactivated and cancel it
        $event->user->autoReactivate();
    }
}
