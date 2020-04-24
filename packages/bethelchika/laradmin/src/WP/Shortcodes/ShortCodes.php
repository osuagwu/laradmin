<?php
namespace BethelChika\Laradmin\WP\Shortcodes;

use Illuminate\Support\Facades\Route;
use BethelChika\Laradmin\Content\ContentManager;
use BethelChika\Laradmin\WP\Models\Post;
use Corcel\Model\Attachment;
use BethelChika\Laradmin\Tools\Tools;

class Shortcodes{

    /**
    * Interpretes a route shortcode
     * Instruction: The shortcode should define 'name'=>route  and an optional 'params':
     * name: A named route
     * text:[optional] Link text
     * params:[optional] The parameters of the route:  given as a name:value pair with pairs separated with commas. e.g. define params with id=1 and lang=en: params=id:1,lang:en 
     * Examples:
     *  [route name=user-settings text="Contact us"] 
     *  [route name=user-profile params=id:1,lang:en] => http://dev.eziulo.com/u/profile?id=1&lang=en
     *  
     * 
     * @param \Thunder\Shortcode\Shortcode\ShortcodeInterface $shortcode
     * @return string
     */
    public static function route($shortcode){
      
        $params=$shortcode->getParameters();

        $href='';
       
        if(isset($params['name']) and Route::has($params['name'])){
            
            
            if(isset($params['params'])){
                $ps=[];
                foreach(explode(',',$params['params']) as $p){
                    $v=explode(':',$p);
                    if(count($v)<2) continue;
                    $ps[$v[0]]=$v[1];
                }
                $href=route($params['name'],$ps);
            }
            else {
                $href=route($params['name']);
            }
            
            $text=$params['text']??$href;
            return '<a href="'.$href.'">'.$text.'</a>';

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
     *  e.g: [hero_url url=u/home/ text="User home"]
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
     * Interpretes shortcode for page_part. This shortcode is used to include 
     * Page parts custom post type in any part of a page. Try not to create a loop by 
     * including a page part into itself. 
     * 
     * Instruction: parameters include:
     * 'name': string a comma separated list of page parts to include.
     *  e.g: [page_part name=widgets]
     */
    public static function pagePart($shortcode){
        $params=$shortcode->getParameters();
        if(isset($params['name'])){
            $page_parts=$params['name'];
        }
            
        else{
            return '';
        }
        
        return Post::getPageParts(explode(',',$page_parts));
    }
    

/**
     * Interpretes shortcode for push. The push shortcode should be used 
     * to push contents from main content to other part of a page. 
     * 
     * Instruction: parameters include:
     * content : The content to be pushed .Should be omitted when content is inside the shortcode.
     * bar : {sidebar-top/bottom,mainbar-top/bottom,rightbar-top/bottom 
     *                  (But essentially all stacks defined in ContentManager are eligible)}. 
     *                  The content will be placed on the specified bar of the page. If not 
     *                  given the content will be place where it is defined.  
     * title [optinal]: The title of the content.
     * 
     * e.g: 
     * Simple example__________________
     * [push bar=sidebar-top content="The bar content"]
     * 
     * Example of nested shortcode_________________
     * [push bar=sidebar-top]
     *    <div class="scroll-y-lg no-scroll-x mCustomScrollbar" data-mcs-theme="minimal-dark">
     *      <h3>The title</h3>
     *          <div class="inner-content">
     *              [menu tag=primary]
     *          </div>
     *      </div>
     * [/push]
     * 
     * @param \Thunder\Shortcode\Shortcode\ShortcodeInterface $shortcode
     * @return string
     */
    public static function push($shortcode){
        $params=$shortcode->getParameters();
        $content=$params['content']??$shortcode->getContent()??'';        
        $bar=$params['bar']??false;
        $title=$params['title']??'';

        if($title){
            $title='<h3>'.$title.'</h3>';
        }
        
        $m= $title. $content;  
        
        if($bar){
            if(in_array($bar,ContentManager::getStacks())){
                ContentManager::registerStack($bar,null,$m);
                return '';
            }
        }
        return $m;
        
    }

    /**
     * Interpretes shortcode for menu
     * 
     * Instruction: parameters include:
     * tag : The menu tag
     * layout [optional]: Menu layout {vertical(default),horizontal}
     * class  [optional]: CSS class for <li>
     * box_class  [optional]: CSS class for the overal menu <ul>
     * bar [optional]: {sidebar-top/bottom,mainbar-top/bottom,rightbar-top/bottom 
     *                  (But essentially all stacks defined in ContentManager are eligible)}. 
     *                  The menu will be placed on the specified bar of the page. If not 
     *                  given the menu will b place where it id defined.
     * title [optinal]: The title of the menu.
     * e.g: [menu tag=primary title="Main menu"]
     * 
     * @param \Thunder\Shortcode\Shortcode\ShortcodeInterface $shortcode
     * @return string
     */
    public static function menu($shortcode){
        $params=$shortcode->getParameters();
        $tag=$params['tag']??'';
        if(!$tag){
            return '';
        }
        $layout=$params['layout']??'vertical';
        $box_class=$params['box_class']??'';
        $class=$params['class']??'';
        $bar=$params['bar']??false;
        $title=$params['title']??'';

        if($title){
            $title='<h3>'.$title.'</h3>';
        }

        $m=view('laradmin::menu',compact('tag','layout','class'))->render();
        $m= $title.'<ul class="nav '.$box_class.'">'.$m.'</ul>';  
        
        if($bar){
            if(in_array($bar,ContentManager::getStacks())){
                ContentManager::registerStack($bar,null,$m);
                return '';
            }
        }
        return $m;
        
    }


/**
 * Interpretes the shortcode for feeds
 * 
 * Instruction: parameters includes
 * allow_fetch_on_scroll [optional]: String{'true', 'false'} See feeds view fo more information.
 * box_class: Css class for the feeds box, e.g {flat-design=>removes rounded corners}.
 * @param \Thunder\Shortcode\Shortcode\ShortcodeInterface $shortcode
 * @return string
 */
    public static function feeds($shortcode){
        $params=$shortcode->getParameters();
        $allow_fetch_on_scroll='false';
        if(isset($params['allow_fetch_on_scroll'] )and $params['allow_fetch_on_scroll']){
            $allow_fetch_on_scroll='true';
        }
        $box_class=$params['box_class']??'flat-design';
        
        $v=view('laradmin::partials.feed.feeds',['allow_fetch_on_scroll'=>$allow_fetch_on_scroll, 'box_class'=>$box_class]);
        return $v->render();
    }

    /**
 * Interpretes the shortcode for social_feeds
 * 
 * Instruction: parameters includes
 * title [optional]: string A title for the feeds.
 * box_class [optional]: string Arbitrary Css class for the feeds box.
 * limit [optional] int The max number of feeds to print
 * @param \Thunder\Shortcode\Shortcode\ShortcodeInterface $shortcode
 * @return string
 */
public static function socialFeeds($shortcode){
    $climit=config('laradmin.social_feeds.limit');
    if(!$climit){
        return '';
    }
    $params=$shortcode->getParameters();
    $title=$params['title']??'';
    $limit=$params['limit']??$climit;
    $box_class=$params['box_class']??'';
    
    // TODO: Check first that the current theme does not have this view before trying to load it from default.
    $v=view(app('laradmin')->theme->defaultFrom().'social.feed.feeds',['limit'=>$limit,'title'=>$title, 'box_class'=>$box_class]);
    return $v->render();
}

/**
 * Interpretes the shortcode for facebook_page
 * 
 * Instruction: parameters includes
 * url [optional]: string the url of the page. Default to the config('services.facebook.page_url')
 * page_name [optional]: string A title for the page default to site name
 * box_class [optional]: string Arbitrary Css class for the contaning box.
 * @param \Thunder\Shortcode\Shortcode\ShortcodeInterface $shortcode
 * @return string
 */
public static function facebookPage($shortcode){
    
    $params=$shortcode->getParameters();
    $url=$params['url']??config('services.facebook.page_url');
    if(!$url){
        return '';
    }

    $page_name=$params['page_name']??config('app.name');
    $box_class=$params['box_class']??'';
    
    $v=view(app('laradmin')->theme->defaultFrom().'social.inc.facebook_page',['url'=>$url,'page_name'=>$page_name, 'box_class'=>$box_class]);
    return $v->render();
}


   

    /**
 * Interpretes the shortcode for contact_form
 * 
 * Instruction: parameters includes
 * return_url [optional]: The url to return to after success.
 * parent_id [optional]: The integer parent message id for message that will be created from the contact form.
 *  
 * @param \Thunder\Shortcode\Shortcode\ShortcodeInterface $shortcode
 * @return string
 */
public static function contactForm($shortcode){
    $params=$shortcode->getParameters();
    
    /////////////////
    $parent_id='';
    $request=request();
    if($request->parent_id){
        $parent_id=$request->parent_id;
    }
    ////////////////////////////////////////////////////////////////////

    $box_class=$params['box_class']??'flat-design';

    $returnToUrl=$params['return_url']??'';
    
    $v=view('laradmin::user.message.contact_us.form',compact('returnToUrl','parent_id'));
    return $v->render();
}


    /**
 * Interpretes the shortcode for posts
 * 
 * Instruction: parameters includes
 * count [optional]: the max number of posts
 * class [optional]: eg:{flat=>no rounded corners} The css class foreach individual post
 * box_class [optional]: The css class for wrapper of all posts.
 * show_summary [optional]: {0,1} When = 1 the the post summaries are included 
 * layout [optional]: {horizonttal,vertical(default)} Determines the layout.
 *  
 * @param \Thunder\Shortcode\Shortcode\ShortcodeInterface $shortcode
 * @return string
 */
public static function posts($shortcode){
    $params=$shortcode->getParameters();
    $count=$params['count']??4;
    $class=$params['class']??'flat';
    $box_class=$params['box_class']??'';
    $summary=$params['show_summary']??0;
    $layout=$params['layout']??'vertical';

    $posts = Post::where('post_type', 'post')->where('post_status', 'publish')->latest()->limit($count)->get();
    $v=view(app('laradmin')->theme->defaultFrom().'wp.partials.blog_posts',['posts'=>$posts,
                                                        'summary'=>$summary,
                                                        'class'=>$class,
                                                        'box_class'=>$box_class,
                                                        'layout'=>$layout]);
    return $v->render();
 
}

   /**
 * Interpretes the shortcode for image_responsive
 * 
 * Instruction: The underlying image must have alt 
 * Parameters includes:
 * id :The id of the image in wp
 * sm [optional]:The size the image should take in the on small screen, Takes similar value like the 'sizes' attributes of img tag, so no % allowed.
 * md [optional]:The equivalent of sm but for medium screen
 * lg [optional]:The equivalent of sm but for large screen.
 *  
 * Example:
 *   [image_responsive id=23 lg=35vw md=35vw sm="calc( 100vw - 30px)"] 
 *      <img src="/img.jpg" alt="The image" > 
 *   [/image_responsive]
 * 
 * @param \Thunder\Shortcode\Shortcode\ShortcodeInterface $shortcode
 * @return string
 */
public static function imageResponsive($shortcode){
    $params=$shortcode->getParameters();
    $id=$params['id']??null;
    $content=$shortcode->getContent()??'';  
    
    $attachment=null;
    if(!$id){
        return $content;
        
    }



    $attachment = Attachment::find($id);
    if(!$attachment){
        return $content;
    }
    
    
    $sm=$params['sm']??'calc( 100vw - 30px)';
    $md=$params['md']??'35vw';
    $lg=$params['lg']??'35vw';


    // Parse the content as html
    $attrs=[];
    $class='image-responsive-limits';
    $dom=new \DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML( $content);
    if($img = $dom->getElementsByTagName('img')->item(0)) {
        foreach ($img->attributes as $attr) {
            if(in_array($attr->nodeName,['src','srcset','sizes','width','height'])){//We ignore width and height attributes since the image should responsive.
                continue;
            }
            if(str_is('class',$attr->nodeName)){
                $class=$class.' '.$attr->nodeValue;
                continue;
            }
            $attrs[$attr->nodeName] = $attr->nodeValue;
            //print_r($attrs);////////////////////////////TODO: delete
        }
        //dd($attrs);////////////////////////////TODO: delete
    }
    $attrs['class']=$class;


    

    $sizes=['(max-width:768px) '.$sm,
            '(max-width:992px) '.$md,
            $lg
            ];

        
    $srcset=Post::getImageSrcset($attachment);

    return (view(app('laradmin')->theme->defaultFrom().'wp.partials.img_srcset',['srcset'=>$srcset,'sizes'=>$sizes,'attrs'=>$attrs])->render());
    
    

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
            $resp=json_decode(Tools::urlGetContent($src));
            
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

//     /**
//      * Fetch content of a URL
//      *
//      * @param string $URL The URL to fetch
//      * @return string
//      */
//     private static function url_get_content($URL){
//         $ch = curl_init();
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//         curl_setopt($ch, CURLOPT_URL, $URL);
//         $data = curl_exec($ch);
//         curl_close($ch);
//         return $data;
//   }
  
  
    
}