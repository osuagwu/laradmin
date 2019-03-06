<?php

namespace BethelChika\Laradmin\WP;

use Corcel\Laravel\CorcelServiceProvider;
use Corcel\Model\Post;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use BethelChika\Laradmin\WP\Shortcodes\ShortCodes;

class WPServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //Register providers
        $this->app->register(CorcelServiceProvider::class);


    }

    /**
     * Bootstrap the application services.
     * 
     * @param \Illuminate\Routing\Router $router
     * @return void
     */
    public function boot(Router $router)
    {

        //Shortcodes
        Post::addShortcode('route', function ($shortcode) {
            return ShortCodes::route($shortcode);
        });

        Post::addShortcode('hero_route', function ($shortcode) {
            return ShortCodes::heroRoute($shortcode);
        });

        Post::addShortcode('hero_url', function ($shortcode) {
            return ShortCodes::heroUrl($shortcode);
        });

        //Short code for embed
        Post::addShortcode('embed', function ($shortcode) {
            return ShortCodes::embed($shortcode);
        });
        


        $menu_names =['primary'];//TODO: create a this in config with a list of menu names/tag 
        foreach($menu_names as $mn){
            WP::exportNavigation($mn);
        }


    }




}
