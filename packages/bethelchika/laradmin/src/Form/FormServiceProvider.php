<?php

namespace BethelChika\Laradmin\Form;

use BethelChika\Laradmin\Laradmin;
use Illuminate\Support\ServiceProvider;

class FormServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //Register singleton
        $this->app->singleton('BethelChika\Laradmin\Form\FormManager', function ($app) {
            return new \BethelChika\Laradmin\Form\FormManager();
        });
        //$this->app->alias('BethelChika\Laradmin\Plugin\PluginManager','pluginmanager');//OPEN THIS if you want to access the singleton directly instead of through laradmin

        // Attach to Laradmin so that we can access it through laradmin
        $this->app->make('laradmin')->formManager=$this->app->make('BethelChika\Laradmin\Form\FormManager');
        
    }

    /**
     * Bootstrap the application services.
     * 
     * @return void
     */
    public function boot(Laradmin $laradmin)
    {
        //Register defaultvforms
        //$laradmin->formManager->registerAutoform('user_settings','profile',\BethelChika\Laradmin\Form\ExampleForms\UserSettingsAutoform::class);
    }

    
}
