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
        // Do not do anything if wp is not enabled
        if(!config('laradmin.wp_enable',true)){
            return;
        }

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
        // Do not do anything if wp is not enabled
        if(!config('laradmin.wp_enable',true)){
            return;
        }

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

        //Short code for push
        Post::addShortcode('push', function ($shortcode) {
            return ShortCodes::push($shortcode);
        });

        //Short code for menu
        Post::addShortcode('menu', function ($shortcode) {
            return ShortCodes::menu($shortcode);
        });

        //Short code for feeds
        Post::addShortcode('feeds', function ($shortcode) {
            return ShortCodes::feeds($shortcode);
        });


         //Short code for contact form
         Post::addShortcode('contact_form', function ($shortcode) {
            return ShortCodes::contactForm($shortcode);
        });

        //Short code for posts
        Post::addShortcode('posts', function ($shortcode) {
            return ShortCodes::posts($shortcode);
        });
        

        //Short code for embed
        Post::addShortcode('embed', function ($shortcode) {
            return ShortCodes::embed($shortcode);
        });


        //Short code for image_responsive
        Post::addShortcode('image_responsive', function ($shortcode) {
            return ShortCodes::imageResponsive($shortcode);
        });


        
        


        // Import menus
        $menu_names =config('laradmin.wp_menus',[]); 
        foreach($menu_names as $mn){
            WP::exportNavigation($mn);
        }


    }




}
