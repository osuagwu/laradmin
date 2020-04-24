<?php

namespace BethelChika\Laradmin\WP;

//use Corcel\Laravel\CorcelServiceProvider;
use Corcel\Model\Post;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use BethelChika\Laradmin\WP\Shortcodes\Shortcodes;

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
        if(!config('laradmin.wp_enable',false)){
            return;
        }

        //Register providers
        //$this->app->register(CorcelServiceProvider::class);


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
        if(!config('laradmin.wp_enable',false)){
            return;
        }

        //Shortcodes
        Post::addShortcode('route', function ($shortcode) {
            return Shortcodes::route($shortcode);
        });

        Post::addShortcode('hero_route', function ($shortcode) {
            return Shortcodes::heroRoute($shortcode);
        });

        Post::addShortcode('hero_url', function ($shortcode) {
            return Shortcodes::heroUrl($shortcode);
        });

        //Short code for page_part
        Post::addShortcode('page_part', function ($shortcode) {
            return Shortcodes::pagePart($shortcode);
        });

        //Short code for push
        Post::addShortcode('push', function ($shortcode) {
            return Shortcodes::push($shortcode);
        });

        //Short code for menu
        Post::addShortcode('menu', function ($shortcode) {
            return Shortcodes::menu($shortcode);
        });

        //Short code for feeds
        Post::addShortcode('feeds', function ($shortcode) {
            return Shortcodes::feeds($shortcode);
        });

        //Short code for social_feeds
        Post::addShortcode('social_feeds', function ($shortcode) {
            return Shortcodes::socialFeeds($shortcode);
        });

        //Short code for facebook_page
        Post::addShortcode('facebook_page', function ($shortcode) {
            return Shortcodes::facebookPage($shortcode);
        });


         //Short code for contact form
         Post::addShortcode('contact_form', function ($shortcode) {
            return Shortcodes::contactForm($shortcode);
        });

        //Short code for posts
        Post::addShortcode('posts', function ($shortcode) {
            return Shortcodes::posts($shortcode);
        });
        

        //Short code for embed
        Post::addShortcode('embed', function ($shortcode) {
            return Shortcodes::embed($shortcode);
        });


        //Short code for image_responsive
        Post::addShortcode('image_responsive', function ($shortcode) {
            return Shortcodes::imageResponsive($shortcode);
        });


        


        // Import menus
        $menu_names =config('laradmin.wp_menus',[]); 
        foreach($menu_names as $mn){
            WP::exportNavigation($mn);
        }


    }




}
