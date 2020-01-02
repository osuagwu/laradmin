<?php

namespace BethelChika\Laradmin\Listeners;

use BethelChika\Laradmin\UserMessage;
use BethelChika\Laradmin\Events\UserHardDelete;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OnUserDelete
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
     * @param  UserHardDelete  $event
     * @return void
     */
    public function handle(UserHardDelete $event)
    {
        //destroy all user messages
        UserMessage::destroyMessagesByUser($event->user);

        //destroy all notifications
        $event->user->destroyNotifications();

        
    }
}