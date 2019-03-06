<?php
namespace BethelChika\Laradmin\WP\Shortcodes;

use Illuminate\Support\Facades\Route;



class Shortcodes{

    /**
    * Interpretes a route shortcode
     * Instruction: The shortcode should define 'name'=>route  and an optional 'params':
     * name: A named route
     * params: The parameters of the route:  given as a name:value pair with pairs separated with commas. e.g. define params with id=1 and lang=en: params=id:1,lang:en 
     * Examples:
     *  [route name=user-settings]
     *  [route name=user-profile params=id:1,lang:en]
     *  
     * 
     * @param \Thunder\Shortcode\Shortcode\ShortcodeInterface $shortcode
     * @return string
     */
    public static function route($shortcode){   
        $params=$shortcode->getParameters();

        if(isset($params['name']) and Route::has($params['name'])){
            if(isset($params['params'])){
                $ps=[];
                foreach(explode(',',$params['params']) as $p){
                    $v=explode(':',$p);
                    if(count($v)<2) continue;
                    $ps[$v[0]]=$v[1];
                }

                return route($params['name'],$ps);
            }
            else return route($params['name']);
        }else {
            return '';
        }
        
    }


    /**
     * Interpretes shortcode for hero_route
     *  Instruction: In addition to the parameters for 'route' shortcode, a parameter 'text' should provided for the link text. Note that if URL i defined result for hero_url shortcode is returned.
     *  e.g: [hero_route name=user-settings text=User settings]
     * 
     * @param \Thunder\Shortcode\Shortcode\ShortcodeInterface $shortcode
     * @return string
     */
    public static function heroRoute($shortcode){
        $params=$shortcode->getParameters();
        $href='/';
        $text='link text';

        if(isset($params['url'])){
            return self::heroUrl($shortcode);
        }else {
            $href=self::route($shortcode);
        }
        if(isset($params['text'])){
            $text=$params['text'];
        }
        return '<a class="btn-hero" href="'.$href.'">'.$text.'</a>';
    }

     /**
     * Interpretes shortcode for hero_url
     *  Instruction: parameters include 'url' and 'text' for like text.
     *  e.g: [hero_url url=u/home/ text='User home']
     * 
     * @param \Thunder\Shortcode\Shortcode\ShortcodeInterface $shortcode
     * @return string
     */
    public static function heroUrl($shortcode){
        $params=$shortcode->getParameters();
        $href='/';
        $text='link text';

        if(isset($params['url'])){
            $href=$params['url'];
        }

        if(isset($params['text'])){
            $text=$params['text'];
        }
        
        return '<a class="btn-hero" href="'.$href.'">'.$text.'</a>';
    }


    /**
     * A temporary implementation of embed shortcode for embedding videos etc. But only youtube videos is currently implemented
     * 
     * Instruction: parameters include max 'width' in pixels, max 'height' in pixels, 'src' which can also be given as the shortcode content, and 'caption'.
     *
     * @param [type] $shortcode
     * @return void
     */
    public static function embed($shortcode){
        $provider='';
        $params=$shortcode->getParameters();
        
        $src='';
        if(isset($params['src'])){
            $src=$params['src'];
        }else{
            $src=$shortcode->getContent();
        }

        $width=null;//NOTE: Currently unused
        if(isset($params['width'])){
            $width=$params['width'];
        }

        $height=null;// NOTE: Currently unused
        if(isset($params['height'])){
            $height=$params['height'];
        }

        $caption=$params['caption']??'';
        if($caption){
            $caption='<div class="caption">'.$caption.'</div>';
        }



        $resp=null;

        // Provider=Youtube .TODO: Need to add support for other providers.
        if(starts_with($src,['https://youtu','https://www.youtu','http://youtu','http://www.youtu'])){
            $provider='youtube';
            $src='http://www.youtube.com/oembed?url='.urlencode($src);
            $resp=json_decode(self::url_get_content($src));
            
        }

        
        
        if(!$resp and !isset($resp->html)){//TODO: Need to properly check the data type and process accordingly rather than hoping that they all have html, see:https://github.com/WordPress/WordPress/blob/97cb2375488c717e02aa6872672b97523ffb6d85/wp-includes/class-oembed.php#L657 
            return '<a class="embed-link '.$provider.'" href="'.$src.'">Link</a>';
        }
        
        
        return '<div class="embeded embeded-auto '.$provider.'">
                    <div class="row padding-top-x5 padding-bottom-x5">
                        <div class="col-md-8 col-md-offset-2 col-xs-12">
                            <div class="embed-responsive embed-responsive-16by9">
                                <p class="embed-responsive-item">'
                                    .$resp->html
                                .'</p>
                            </div>'
                            .$caption
                        .'</div>
                    </div>
                </div>';
    }

    private static function url_get_content($URL){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $URL);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
  }
  
  
    
}