<?php

namespace BethelChika\Ulooma;

use BethelChika\Laradmin\Laradmin;
use Illuminate\Support\ServiceProvider;


class UloomaServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Bootstrap the application services.
     * 
     * @return void
     */
    public function boot(Laradmin $laradmin)
    {
        
        $laradmin->theme=new UloomaPlugable;

        
    }

    
}
