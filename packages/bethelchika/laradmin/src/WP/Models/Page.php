<?php
namespace BethelChika\Laradmin\WP\Models;

use BethelChika\Laradmin\Source;

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

    /**
     * Check if authentication/authorisation is needed. If a page has at least one 
     * entry then it requires authentication since it will be required in order 
     * to authorise the entry.
     *
     * @return boolean
     */
    public function needsAuth(){
        // Check at the table and page level
        $access_string=Source::getTableAccessString($this);
        $perm=app('laradmin')->permission;
        $page_access_string=Source::getPageTypeKey().':'.$this->getKey();
        if ($perm->hasEntry($access_string,'read') or 
            $perm->hasEntry($page_access_string,'read')){
            return true;
        }

        //Check at the model level
        $source=Source::where('type','model')->where('name',get_class())->first();
        if($source){
            $model_access_string=Source::getTypeKey().':'.$source->id;
            if ($perm->hasEntry($model_access_string,'read')) {
                return true;
            }
        }


        // Does not need auth
        return false;
    }
}
