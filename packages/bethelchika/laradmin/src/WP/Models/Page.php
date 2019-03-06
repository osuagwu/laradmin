<?php
namespace BethelChika\Laradmin\WP\Models;
//use \Corcel\Model\Page as CorcelPage;
//use \Corcel\Model\Post as CorcelPost;

//use BethelChika\Laradmin\WP\Traits\Formatting;
class Page extends Post
{

//use Formatting;
   
    // /**
    // * Get the Url of the spicified object
    // * @return string Url of the object
    //  */
    // public function makeUrl(){
    //     return '/'.config('laradmin.page_url_prefix').'/'.$this->post_name;
    // }

    // /**
    // * Get the Url of the spicified object
    // * @return string Url of the object
    //  */
    //  public static function corcelMakeUrl(CorcelPost $page){
    //     return '/'.config('laradmin.page_url_prefix').'/'.$page->post_name;
    // }

    /**
     * Returns the sidebar laradmin_page_type content
     *
     * @return string
     */
    public function getSidebar(){
        return Post::where('post_type','laradmin_page_part')->where('post_name', 'sidebar')->first()->content;
    }

    /**
     * Returns the  rightbar laradmin_page_type content
     *
     * @return string
     */
    public function getRightbar(){
        return Post::where('post_type','laradmin_page_part')->where('post_name', 'rightbar')->first()->content;
    }

    /**
     * Returns the footer laradmin_page_type content
     *
     * @return string
     */
    public function getFooter(){
        return Post::where('post_type','laradmin_page_part')->where('post_name', 'footer')->first()->content;
    }


}
