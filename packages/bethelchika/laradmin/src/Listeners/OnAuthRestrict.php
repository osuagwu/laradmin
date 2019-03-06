<?php

namespace BethelChika\Laradmin\Listeners;

use BethelChika\Laradmin\Events\AuthRestrict;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OnAuthRestrict
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
     * @param  AuthRestrict  $event
     * @return void
     */
    public function handle(AuthRestrict $event)
    {
        $event->user->authRestrict();
    }
}
