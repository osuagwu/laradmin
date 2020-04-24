<?php

namespace BethelChika\Laradmin\Http\Controllers\User\Traits\WP;

use BethelChika\Laradmin\WP\Models\Post;
use BethelChika\Laradmin\WP\Models\HomepageSection;


trait Homepage
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
        $post=$hpss->shift();


        if(!$post){
            return abort(404);//'Laradmin homepage section not defined in WP'
        }


        //dd($post->getFeaturedThumbSrcset());

        $tpl=$this->laradmin->theme->from.'wp.page_templates.homepage';
        $tpl_default = '';
        

        // Define metas;
        $metas['url'] = url()->current();
        $metas['type'] = 'article';
        $metas['title'] = $post->title;
        $metas['description'] = $post->post_excerpt ? $post->post_excerpt : strip_tags(str_limit($post->post_content, 280,'...'));
        $metas['image'] = $post->image;//TODO: check that this is right
        $metas['tweet'] = str_finish($post->post_excerpt, 277,'...') . '#' . config('app.name');
        

        // We assume that the first page is a hero if it has has the hero_type meta set. 
        //See BethelChika\Laradmin\Http\Controllers\User\WPController->page() for information of custom fields/meta
        //And to avoid making the page too heavy we only allow the first section to be a hero.
        if($post->meta->hero_type)     
        {
           $metas['hero']=$this->content2Hero($post);
           $this->laradmin->assetManager->registerHero($post->getHeroImages(),$post->meta->hero_type);
        }else{
            $hpss->prepend($post);
        }

        $posts = Post::where('post_type', 'post')->where('post_status', 'publish')->latest()->limit($post->meta->blog_listing_count??4)->get();
        $pageTitle=$post->title;
        return view($this->laradmin->theme->defaultFrom().'wp.page', compact('pageTitle','tpl','tpl_default', 'post', 'hpss', 'posts','metas'));
    }
}