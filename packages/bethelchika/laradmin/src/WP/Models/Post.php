<?php
namespace BethelChika\Laradmin\WP\Models;
use Corcel\Model\Post as CorcelPost;
use Corcel\Model\Meta\ThumbnailMeta;
use BethelChika\Laradmin\WP\Traits\Formatting;
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
     *
     * @param integer $width Pixel
     * @param integer $height Pixel
     * @param boolean $or_any another size is returned of the specified size is does not exist
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
     * @param boolean $or_any another size is returned of the specified size is does not exist
     * @return string
     */
    public function getFeaturedThumb($size_name=ThumbnailMeta::SIZE_MEDIUM){
        
        $thumb=$this->thumbnail->size($size_name);
        if(is_array($thumb))$thumb=$thumb['url'];
        return $thumb;
        
    }
}
