<?php

namespace BethelChika\Laradmin\Listeners;

use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OnNotificationSent
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
     * @param  NotificationSent  $event
     * @return void
     */
    public function handle(NotificationSent $event)
    {
        // $event->channel
        // $event->notifiable
        // $event->notification
        
        $channel=$event->channel;
        if(!is_array($channel)){ //It might actually be string that we receive as channels, but converting to array is no harm
            $channel=[$channel];
        }

        if(in_array('database',$channel)){//If this notification is going to our database invoke a function to make sure the concerned user do not have too many notifications
            $event->notifiable->limitNotifications();
        }
    }
}
