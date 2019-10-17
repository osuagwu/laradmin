<?php

namespace BethelChika\Laradmin;

use Illuminate\Routing\Router;
use BethelChika\Laradmin\Laradmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use BethelChika\Laradmin\Feed\FeedManager;
use Illuminate\View\Factory as ViewFactory;
use BethelChika\Laradmin\Asset\AssetManager;
use BethelChika\Laradmin\Media\MediaManager;
use Intervention\Image\ImageServiceProvider;
use BethelChika\Laradmin\Notifications\Notice;
use BethelChika\Laradmin\WP\WPServiceProvider;
use BethelChika\Laradmin\Permission\Permission;
use BethelChika\Laradmin\Content\ContentManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Cookie\Middleware\EncryptCookies;
use BethelChika\Laradmin\Form\FormServiceProvider;
use BethelChika\Laradmin\Menu\MenuServiceProvider;
use BethelChika\Laradmin\Plugin\PluginServiceProvider;
use BethelChika\Laradmin\Providers\AuthServiceProvider;
use BethelChika\Laradmin\Providers\EventServiceProvider;
use BethelChika\Laradmin\Http\Middleware\CheckReAuthentication;
use BethelChika\Laradmin\Providers\BladeDirectivesServiceProvider;
use BethelChika\Laradmin\Theme\DefaultTheme;

class LaradminServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // //Register Laradmin singleton
        // $this->app->singleton('BethelChika\Laradmin\Laradmin', function ($app) {
        //     return new Laradmin(new MediaManager($app->make('filesystem')),new FeedManager,new AssetManager, new ContentManager,new Permission );
        // });
        // $this->app->alias('BethelChika\Laradmin\Laradmin','laradmin');
 
         //Register Laradmin singleton
         $this->app->singleton('laradmin', function ($app) {
            return new Laradmin(new MediaManager($app->make('filesystem')),new FeedManager,new AssetManager, new ContentManager,new Permission, new DefaultTheme );
        });
        $this->app->alias('laradmin','BethelChika\Laradmin\Laradmin');

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

        // Register wordpress bridge which will used for pages menus etc
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

        // Register CP fieldables
        $laradmin->formManager->registerFieldable('cp_settings','general',\BethelChika\Laradmin\Tools\Forms\CPSettingsFieldable::class);

        // Register User Fieldables
        $laradmin->formManager->registerFieldable('user_settings','personal',\BethelChika\Laradmin\Tools\Forms\ProfileFieldable::class);
        //$laradmin->formManager->registerFieldable('user_settings','address',\BethelChika\Laradmin\Tools\Forms\ProfileContactsFieldable::class);
        $laradmin->formManager->registerFieldable('user_settings','preference',\BethelChika\Laradmin\Tools\Forms\ProfilePreferenceFieldable::class);
        
        
        

        
        //Share a view of list of plugins
        //$this->sharePluginsList($laradmin,$view);


        // Share laradmin to all views
        $view->share('laradmin', $laradmin);


        
        // Check if this user has login restrictions and prevent him from login in
        if(Auth::check()){
            if(Auth::user()->hasLoginRestrictions()){
                //Auth::logout();
                redirect()->route('dashboard')->with('danger','This account is restricted')->send();
            }
        }

        
        

        // Cookie consent: Let us not encript the consent cookie
        $this->app->resolving(EncryptCookies::class, function (EncryptCookies $encryptCookies) {
            $encryptCookies->disableFor(config('laradmin.cookie_consent.name'));
        });
        

        
        // Publish things & loading__________________________________________________
        
        $laradminPath=dirname(__DIR__);

        // Route Middlewares
        $router->aliasMiddleware('re-auth',CheckReAuthentication::class);
        $router->aliasMiddleware('pre-authorise','BethelChika\Laradmin\Permission\Http\Middleware\PreAuthorise');
        
        // Middleware groups
        $router->pushMiddlewareToGroup('web','BethelChika\Laradmin\AuthVerification\Http\Middleware\AuthVerification');
        $router->pushMiddlewareToGroup('web','BethelChika\Laradmin\Http\Middleware\Preference');
        
        
        // Load route
        $this->loadRoutesFrom($laradminPath.'/routes/web.php');

        //dd($_SERVER);
        // Detect when we are in admin and load the cp routes
        // NOTE: If this gives problem just merge the cp_wep.php into web.php or just load cp_web.php all the time
        if($this->app->runningInConsole() or strpos($_SERVER['REQUEST_URI'],'/cp/')===0 ){// NOTE: this prohibits the use of 'domain/cp' i.e with the trailing slash '/'
            $this->loadRoutesFrom($laradminPath.'/routes/cp_web.php');
        }
        



        // Translations
        $this->loadTranslationsFrom($laradminPath.'/resources/lang', 'laradmin');
        

       

        // Views
        $this->loadViewsFrom($laradminPath.'/resources/views', 'laradmin');
        

        if ($this->app->runningInConsole()) {
            // Publish confi
            $this->publishes(
                [$laradminPath.'/config/laradmin.php'=>config_path('laradmin.php')], 'laradmin-config'
            );
            
            // Load migrations
            $this->loadMigrationsFrom($laradminPath.'/database/migrations');

            $this->publishes([
                $laradminPath.'/resources/lang' => resource_path('lang/vendor/laradmin'),
            ],'laradmin-lang');


            $this->publishes([
                $laradminPath.'/resources/views' => resource_path('views/vendor/laradmin'),
            ], 'laradmin-view');
            

            // Assets
            $this->publishes([
                $laradminPath.'/publishable/assets' => public_path('vendor/laradmin'),
            ], 'laradmin-asset');
        }


        
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
