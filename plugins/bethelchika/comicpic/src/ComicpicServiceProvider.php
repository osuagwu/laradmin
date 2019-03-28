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
        $laradmin->navigation->create('Comicpic','comicpic','admin.apps',[
            'cssClass'=>'',
            'namedRoute'=>'comicpic.admin',
            'iconClass'=>'far fa-laugh-wink',
            ]);

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

        //Add a menu to user_apps
        $laradmin->navigation->create($appname,'comicpic','user_apps',[
            'namedRoute'=>'comicpic.index','iconClass'=>'fas fa-laugh-wink']);
        
        // Register fieldables
        $laradmin->formManager->registerFieldable('user_settings','profile',\BethelChika\Comicpic\Form\ComicpicFieldable::class);

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
