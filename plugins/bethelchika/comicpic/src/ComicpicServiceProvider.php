<?php

namespace BethelChika\Comicpic;

use BethelChika\Laradmin\Laradmin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use BethelChika\Comicpic\Feed\ComicpicDynamicFeedable;

class ComicpicServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //echo '<br>Register comic pic';
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

        // Register Plugin
        //$laradmin->pluginManager->registerPlugin(ComicpicPlugable::class);

        // Register feedable
        $laradmin->feedManager->registerFeedable(ComicpicDynamicFeedable::class);

        //Create admin menu
        $admin_nav=$laradmin->navigation->create('Comicpic','comicpic','admin.apps',[
            'cssClass'=>'',
            'namedRoute'=>'comicpic.admin',
            'iconClass'=>'far fa-laugh-wink',
            ]);
        $admin_nav->addDummyNamedRoutes([
            'comicpic.admin-edit-settings',
            'comicpic.admin-show',
            'comicpic.admin-create']);


        // Create menu item
        $appname=Cache::get('comicpic.appname','Comicpic');
        $laradmin->navigation->create($appname,'comicpic','primary',[
            'cssClass'=>'',
            'namedRoute'=>'comicpic.index',
            'iconClass'=>'far fa-laugh-wink',
            ]);
        $laradmin->navigation->create('Browse','browse','primary.comicpic',[
            'namedRoute'=>'comicpic.index','iconClass'=>'fas fa-eye']);
        $laradmin->navigation->create('My '.$appname,'me','primary.comicpic',[
            'namedRoute'=>'comicpic.me','iconClass'=>'far fa-laugh-wink']);
        $laradmin->navigation->create('Upload','upload','primary.comicpic',[
                'namedRoute'=>'comicpic.create','iconClass'=>'fas fa-upload']);
        $laradmin->navigation->create('Settings','settings','primary.comicpic',[
            'namedRoute'=>'comicpic.user_settings','iconClass'=>'fas fa-laugh-wink']);

        //Add a menu to user_apps
        $laradmin->navigation->create($appname,'comicpic','user_apps',[
            'namedRoute'=>'comicpic.index','iconClass'=>'fas fa-laugh-wink']);

        //Add a menu to the user settings
        $laradmin->navigation->create($appname.' settings','comicpic','user_settings',[
            'namedRoute'=>'comicpic.user_settings','iconClass'=>'fas fa-laugh-wink']);
        
        // Register fieldables
        $laradmin->formManager->registerFieldable('user_settings','personal',\BethelChika\Comicpic\Form\ComicpicFieldable::class);
        
        // //delte from here**************************************************************
        // $laradmin->formManager->registerFieldable('user_settings','adresses',\BethelChika\Comicpic\Form\ComicpicFieldable::class);
        // $laradmin->formManager->registerFieldable('user_settings','Loppost',\BethelChika\Comicpic\Form\ComicpicFieldable::class);
        // $laradmin->formManager->registerFieldable('user_settings','credit',\BethelChika\Comicpic\Form\ComicpicFieldable::class);
        // $laradmin->formManager->registerFieldable('user_settings','film_items',\BethelChika\Comicpic\Form\ComicpicFieldable::class);
        // $laradmin->formManager->registerFieldable('user_settings','my_likes',\BethelChika\Comicpic\Form\ComicpicFieldable::class);
        // //delete to here***********************************************

        //Register auto forms
        $laradmin->formManager->registerAutoform('comicpic','user_settings',\BethelChika\Comicpic\Form\ComicpicAutoform::class);
        //Register auto form duplicate just to test multiple form
        $laradmin->formManager->registerAutoform('comicpic','user_settings2',\BethelChika\Comicpic\Form\ComicpicAutoform::class);

        // Add assets that should appear in every page
        //$laradmin->assetManager->addAsset('head-styles','test','<style>.dropzone{background-color:yellow;}</style>');
        

        // Publish stuff__________________________________________________

        // Views
        $this->loadViewsFrom($path.'/resources/views', 'comicpic');
        
        $this->publishes([
            $path.'/resources/views' => resource_path('views/vendor/comicpic'),
        ]); 

        // Assets
        $this->publishes([
            $path.'/publishable' => public_path('vendor/comicpic'),
        ], 'public');

        // Load migrations
        $this->loadMigrationsFrom($path.'/database/migrations');



        
    }

    
}
