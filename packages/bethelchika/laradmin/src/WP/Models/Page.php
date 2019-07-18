<?php
namespace BethelChika\Laradmin\WP\Models;

use BethelChika\Laradmin\Source;


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
     * Returns the sidebar laradmin_page_part content
     *
     * @return string
     */
    public function getSidebar(){
        $c='';
        $sidebars=$this->meta->sidebars;
        if($sidebars){
            return $this->getPageParts(explode(',',$sidebars));
        }
        return $this->getPageParts(['sidebar']);
    }

    /**
     * Returns the  rightbar laradmin_page_part content
     *
     * @return string
     */
    public function getRightbar(){
        $c='';
        $rightbars=$this->meta->rightbars;
        if($rightbars){
            return $this->getPageParts(explode(',',$rightbars));
        }
        return $this->getPageParts(['rightbar']);
    }

    /**
     * Returns the footer laradmin_page_part content
     *
     * @return string
     */
    public function getFooter(){
        $c='';
        $footers=$this->meta->footers;
        if($footers){
           return $this->getPageParts(explode(',',$footers));
        }
        return $this->getPageParts(['footer']);
    }

    /**
     * Get the contents of laradmin page parts specified by array of slugs
     *
     * @param array $slugs Array of slugs for the page parts
     * @return string
     */
    private function getPageParts($slugs){
        if(!count($slugs)){
            return '';
        }
        $c='';
        $q=Post::where('post_type','laradmin_page_part')
        ->where(function($query)use ($slugs){
            foreach($slugs as $slug){
                $query->orwhere('post_name',$slug);
            }
        });
        // foreach($slugs as $slug){
        //     $q->where('post_name', $slug);
        // }
        foreach($q->get() as $r){
            $c=$c.$r->contentFiltered;
        }
        return $c;
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
        $table_source_id=Source::getTableSourceIdFromModel($this);
        $perm=app('laradmin')->permission;
        //$page_access_string=Source::getPageTypeKey().':'.$this->getKey();
        if ($perm->hasDenyEntry('table',$table_source_id,'read') or 
            $perm->hasDenyEntry(get_class(),$this->getKey(),'read')){
            return true;
        }

        //Check at the model level
        $source=Source::where('type','model')->where('name',get_class())->first();
        if($source){
            //$model_access_string=Source::getTypeKey().':'.$source->id;
            //if ($perm->hasEntry(Source::class,$source->id,'read')) {
            if ($perm->hasDenyEntry(Source::class,$source->id,'read')) {
                return true;
            }
        }


        // Does not need auth
        return false;
    }
}
