<?php
namespace BethelChika\Laradmin\WP\Models;
use Corcel\Model\Post as CorcelPost;
use Corcel\Model\Meta\ThumbnailMeta;
use BethelChika\Laradmin\WP\Traits\Formatting;
use Corcel\Model\Attachment;

class Post extends CorcelPost
{

    use Formatting;
   
    // /**
    // * Get the Url of the spicified object
    // * @return string Url of the object
    //  */
    //  public  function makeUrl(){
    //     return '/post/'.$this->post_name;
    // }


    // /**
    // * Get the Url of the spicified object
    // * @return string Url of the object
    //  */
    //  public static function corcelMakeUrl(CorcelPost $post){
    //     return '/post/'.$post->post_name;
    // }

    /**
     * Get the featured image thumb with specific pixel size
     * TODO: NOTE: THIS METHOD IS NOT IN USE
     *
     * @param integer $width Pixel
     * @param integer $height Pixel
     * @param boolean $or_any another size is returned if the specified size is does not exist
     * @return string
     */
    public function getFeaturedThumbSize($width=270,$height=151,$or_any=true){
        $thumb='';
        if($image=$this->image){
            $im=explode('.',$image);
            $im[count($im)-2]=$im[count($im)-2].'-'.$width.'x'.$height;
            
            $thumb=implode('.',$im);
            $thumb_p=str_replace(env('APP_URL'),public_path(),$thumb);
            //print_r($thumb_p);
            if(!file_exists($thumb_p)){
                if($or_any){
                    $thumb=$image; //if the requested size is not found, we will just return the the original
                }
                else{
                    $thumb='';
                }
            }
            

        }
        return $thumb;
    }

    /**
     * Get the featured image thumb  
     * 
     * @param integer $size_name {thumbnail|medium|large|full}
     * @param boolean $or_full Try to return the full size if the specified size does not exist
     * @return string The image url
     */
    public function getFeaturedThumb($size_name=ThumbnailMeta::SIZE_MEDIUM,$or_full=true){
        if($this->thumbnail){
            $thumb=$this->thumbnail->size($size_name);
            if(is_array($thumb)){
                return $thumb['url'];
            } 
            elseif($or_full){
                return $thumb;
            }
        }
        return null;
        
    }
    
    
    /**
     * Returns featured image srcset
     *
     * @return @see self::getImageSrcset()
     */
    public function getFeaturedThumbSrcset(){
        return self::getImageSrcset($this->getFeaturedThumbAttachment());
    }


    /**
     * Returns featured image attachement object
     *
     * @return \Corcel\Model\Attachment
     */
    public function getFeaturedThumbAttachment(){
        
        if ($this->thumbnail and $this->thumbnail->attachment) {
            return $this->thumbnail->attachment;
        }
        return false;
    }

      /**
     * Get the featured image hero sizes  
     *
     * @return array The image urls index by 'sm' and 'lg', which are for small and large screens repectively
     */
    public function getHeroImages(){
        $hi['lg']=null;
        $hi['sm']=null;

        if($this->thumbnail){
            $thumb=$this->thumbnail->size('laradmin-hero-lg');            
            if(is_array($thumb)){
                $hi['lg']=$thumb['url'];
            }else{
                // The requested image is not avaialble so use the full size
                $hi['lg']=$thumb;
            }

            $thumb=$this->thumbnail->size('laradmin-hero-sm');
            if(is_array($thumb)){
                $hi['sm']=$thumb['url'];
            }
        }

        return $hi;
        
    }

    


    /**
     * Get the URL for editing a page
     *
     * @return string
     */
    public function getEditLink(){
        return config('laradmin.wp_rpath').'/wp-admin/post.php?post='.$this->ID.'&action=edit';
                        
    }



     /**
     * Returns image srcset as an array
     *
     * @return array [['url'=>?,'width'=>?,'height'=>?]]
     */
    public static function getImageSrcset(Attachment $attachment){
        $srcset=[];
         
        if ($attachment) {   
            $url=$attachment->guid;//original
            $location=dirname($url);
            $s=unserialize($attachment->_wp_attachment_metadata);
            
            $srcset[]=['url'=>$url,'width'=>$s['width'],'height'=>$s['height']];// Inlude the original image as the first src item id the source set
            foreach($s['sizes'] as $size){
                $url_=$location.'/'.$size['file'];
                $srcset[]=['url'=>$url_,'width'=>$size['width'],'height'=>$size['height']];
            }
           
        }
        return $srcset;
    }
}
