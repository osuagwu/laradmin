<?php

namespace BethelChika\Laradmin\Menu;

use Illuminate\Support\ServiceProvider;
class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //Register Navigation singleton
        $this->app->singleton('BethelChika\Laradmin\Menu\Navigation', function ($app) {
            return new Navigation();
        });
        //$this->app->alias('BethelChika\Laradmin\Menu\Navigation','navigation');//OPEN THIS if you want to access the singleton directly instead of through laradmin

        // Attach Navigation to Laradmin so that we can access it through laradmin
        $this->app->make('laradmin')->navigation=$this->app->make('BethelChika\Laradmin\Menu\Navigation');
        
        // TODO: Not important but this could be moved to the main service provider or to thr boot method
        $this->app->make('BethelChika\Laradmin\Menu\Navigation')->init(config('laradmin.navigation_file','navigation.nav'));//TODO: need to make sure that the config file do not require laradmin config file
        
    }

    /**
     * Bootstrap the application services.
     * 
     * @return void
     */
    public function boot()
    {
        
    
    }

    
}
