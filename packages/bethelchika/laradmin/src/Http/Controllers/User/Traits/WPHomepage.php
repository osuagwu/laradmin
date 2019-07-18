<?php

namespace BethelChika\Laradmin\Http\Controllers\User\Traits;

use BethelChika\Laradmin\WP\Models\Post;
use BethelChika\Laradmin\WP\Models\HomepageSection;


trait WPHomepage
{
    /**
     * Shows home page
     *  @see BethelChika\Laradmin\Http\Controllers\User\WPController->page() for information of custom fields
     * @return \Illuminate\Http\Response
     */
    public function homepage(){
        // Get page sections and order by ascending menu_order column
        $hpss = HomepageSection::published()->orderBy('menu_order','asc')->get();

        //Get the first section (section with lowest menu_order). Note that this is the most important 
        //section whose information is used to create page titles, meta etc. 
        $page=$hpss->shift();


        if(!$page){
            return abort(404);//'Laradmin homepage section not defined in WP'
        }


        //dd($page->getFeaturedThumbSrcset());
        

        // Define metas;
        $metas['url'] = url()->current();
        $metas['type'] = 'article';
        $metas['title'] = $page->title;
        $metas['description'] = $page->post_excerpt ? $page->post_excerpt : strip_tags(str_limit($page->content, 280,'...'));
        $metas['image'] = $page->image;//TODO: check that this is right
        $metas['tweet'] = str_finish($page->post_excerpt, 277,'...') . '#' . config('app.name');
        

        // We assume that the first page is a hero if it has has the hero_type meta set. 
        //See BethelChika\Laradmin\Http\Controllers\User\WPController->page() for information of custom fields/meta
        //And to avoid making the page too heavy we only allow the first section to be a hero.
        if($page->meta->hero_type)     
        {
           $metas['hero']=$this->content2Hero($page);
           $this->laradmin->assetManager->registerHero($page->getHeroImages(),$page->meta->hero_type);
        }else{
            $hpss->prepend($page);
        }

        $posts = Post::where('post_type', 'post')->where('post_status', 'publish')->latest()->limit($page->meta->blog_listing_count??4)->get();
        $pageTitle=$page->title;
        return view('laradmin::user.wp.homepage', compact('pageTitle', 'page', 'hpss', 'posts','metas'));
    }
}