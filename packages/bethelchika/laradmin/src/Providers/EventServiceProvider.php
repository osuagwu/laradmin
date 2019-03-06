<?php

namespace BethelChika\Laradmin\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // 'App\Events\Event' => [
        //     'App\Listeners\EventListener',
        // ],

        
        'BethelChika\Laradmin\Events\UserHardDelete' => [
            'BethelChika\Laradmin\Listeners\OnUserDelete',
        ],
        'Illuminate\Notifications\Events\NotificationSent' => [
            'BethelChika\Laradmin\Listeners\OnNotificationSent',
        ],
        'BethelChika\Laradmin\Events\AuthRestrict' => [
            'BethelChika\Laradmin\Listeners\OnAuthRestrict',
        ],
        'Illuminate\Auth\Events\Login' => [
            'BethelChika\Laradmin\Listeners\OnSuccessfulLogin',
        ],
        'Illuminate\Auth\Events\Logout' => [
            'BethelChika\Laradmin\Listeners\OnSuccessfulLogout',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
