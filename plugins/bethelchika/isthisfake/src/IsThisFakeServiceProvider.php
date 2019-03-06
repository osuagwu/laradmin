<?php

namespace BethelChika\IsThisFake;

use BethelChika\Laradmin\Laradmin;
use Illuminate\Support\ServiceProvider;

class isThisFakeServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //echo '<br>Register isfake news';
    }

    /**
     * Bootstrap the application services.
     * 
     * @return void
     */
    public function boot(Laradmin $laradmin)
    {
        $path=dirname(__DIR__);

        // Load route
        $this->loadRoutesFrom($path.'/routes/web.php');

        

        // Publish stuff__________________________________________________

        // Views
        $this->loadViewsFrom($path.'/resources/views', 'isthisfake');
        
        $this->publishes([
            $path.'/resources/views' => resource_path('views/vendor/isthisfake'),
        ]); 

        // Assets
        $this->publishes([
            $path.'/publishable/assets' => public_path('vendor/isthisfake'),
        ], 'public');
        
    }

    
}
