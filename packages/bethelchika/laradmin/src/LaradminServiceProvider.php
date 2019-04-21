<?php

namespace BethelChika\Laradmin;

use Illuminate\Routing\Router;
use BethelChika\Laradmin\Laradmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use BethelChika\Laradmin\Feed\FeedManager;
use Illuminate\View\Factory as ViewFactory;
use BethelChika\Laradmin\Media\MediaManager;
use Intervention\Image\ImageServiceProvider;
use BethelChika\Laradmin\Notifications\Notice;
use Illuminate\Contracts\Foundation\Application;
use BethelChika\Laradmin\Menu\MenuServiceProvider;
use BethelChika\Laradmin\Providers\AuthServiceProvider;
use BethelChika\Laradmin\Providers\EventServiceProvider;
use BethelChika\Laradmin\Http\Middleware\CheckReAuthentication;
use BethelChika\Laradmin\Providers\BladeDirectivesServiceProvider;
use BethelChika\Laradmin\Asset\AssetManager;
use BethelChika\Laradmin\Content\ContentManager;
use BethelChika\Laradmin\Plugin\PluginServiceProvider;
use BethelChika\Laradmin\WP\WPServiceProvider;
use BethelChika\Laradmin\Form\FormServiceProvider;

class LaradminServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //Register Laradmin singleton
        $this->app->singleton('BethelChika\Laradmin\Laradmin', function ($app) {
            return new Laradmin(new MediaManager($app->make('filesystem')),new FeedManager,new AssetManager, new ContentManager );
        });
        $this->app->alias('BethelChika\Laradmin\Laradmin','laradmin');
 

        //Register providers
        $this->app->register(EventServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(BladeDirectivesServiceProvider::class);
        
        // Register menu service provider
        $this->app->register(MenuServiceProvider::class);

        //Register service provider for plugin
        $this->app->register(PluginServiceProvider::class);

        // Register service providers from other packages 
        $this->app->register(ImageServiceProvider::class); //TODO: this can also be simply be put in the laravel's main config/app instead expecially if the main app is going to use this package already

        // Register wordpress bridge which will pages menus etc
        $this->app->register(WPServiceProvider::class);

        // Register form service provider
        $this->app->register(FormServiceProvider::class);
        
        
    }

    /**
     * Bootstrap the application services.
     * 
     * @param \Illuminate\Routing\Router $router
     * @return void
     */
    public function boot(Router $router,Laradmin $laradmin,ViewFactory $view)
    {
        

        //check if a user  was deactivated and  auto reactivate him
        if(Auth::check()){
            if(Auth::user()->autoReactivate()){
                $view::share('autoUserReactivation', [true,'Your account was reactivated successfully']);
            }else{
                Auth::user()->getSystemUser->notify(new Notice('Auto reactivation fail (User id:'.Auth::user()->id.')'));
                $view::share('autoUserReactivation', [false,'It was not possible to reactivate your account automatically. Please go to your settings page to manually reactivated your account.']);
            }
        }

        // Register fieldables
        $laradmin->formManager->registerFieldable('user_settings','personal',\BethelChika\Laradmin\Tools\Forms\ProfileFieldable::class);
        $laradmin->formManager->registerFieldable('user_settings','contacts',\BethelChika\Laradmin\Tools\Forms\ProfileContactsFieldable::class);
        
        


        //Share a view of list of plugins
        //$this->sharePluginsList($laradmin,$view);


        // Share laradmin to all views
        $view->share('laradmin', $laradmin);


        //Check if this user has login restrictions and prevent him from login in
        if(Auth::check()){
            if(Auth::user()->hasLoginRestrictions()){
                //Auth::logout();
                redirect()->route('dashboard')->with('danger','This account is restricted')->send();
            }
        }


        

        


        // Publish things __________________________________________________
        
        $laradminPath=dirname(__DIR__);

        // Middlewares
        $router->aliasMiddleware('re-auth',CheckReAuthentication::class);

        // Publish confi
        $this->publishes(
            [$laradminPath.'/config/laradmin.php'=>config_path('laradmin.php')]
        );

        // Load route
        $this->loadRoutesFrom($laradminPath.'/routes/web.php');


        // Load migrations
        $this->loadMigrationsFrom($laradminPath.'/database/migrations');


        // Translations
        $this->loadTranslationsFrom($laradminPath.'/resources/lang', 'laradmin');
        
        $this->publishes([
            $laradminPath.'/resources/lang' => resource_path('lang/vendor/laradmin'),
        ]);


        // Views
        $this->loadViewsFrom($laradminPath.'/resources/views', 'laradmin');
        
        $this->publishes([
            $laradminPath.'/resources/views' => resource_path('views/vendor/laradmin'),
        ]);
        

        // Assets
        $this->publishes([
            $laradminPath.'/publishable/assets' => public_path('vendor/laradmin'),
        ], 'public');


        
    }

    // /**
    //  * Share plugins list to the views
    //  *
    //  * @return void
    //  */
    // private function sharePluginsList(Laradmin $laradmin,ViewFactory $view ){
    //     $pluginsList=$laradmin->pluginManager->getAllInfo();
    //     $view->share('plugins', $pluginsList);
        
    // }

   

    
}
