<?php

namespace BethelChika\Laradmin\Http\Controllers\User;

use Corcel\Model\Comment;
use Illuminate\Http\Request;
use BethelChika\Laradmin\User;
use Illuminate\Support\Carbon;
use BethelChika\Laradmin\Laradmin;
use Illuminate\Support\Facades\Auth;
use BethelChika\Laradmin\WP\Models\Page;
use BethelChika\Laradmin\WP\Models\Post;
use BethelChika\Laradmin\WP\Models\LarusPost;
use BethelChika\Laradmin\Http\Controllers\Controller;
use BethelChika\Laradmin\Http\Controllers\User\Traits\WP\Homepage;
use BethelChika\Laradmin\Http\Controllers\User\Traits\WP\PostComment;
use BethelChika\Laradmin\Theme\DefaultTheme;
use Corcel\Model\Option;

class WPController extends Controller
{
    use Homepage;
    use PostComment;

    private $laradmin;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Laradmin $laradmin)
    {
        parent::__construct();
        $this->middleware('auth', ['only' => ['createComment','larusPost']]);
        $this->laradmin = $laradmin;

        
    } 

    /**
     * DO prechecks before a post is displayed
     *
     * @param Post $post
     * @return \Illuminate\Http\Response|null
     */
    private function checkBefore(Post $post){
        

        if($post->needsAuth()){
            if(Auth::guest()){
                $this->laradmin->assetManager->registerBodyClass('main-nav-no-border-bottom');
                return view($this->laradmin->theme->from.'wp.needs_auth');
            }
            if(!Auth::user()->can('view',$post)){
                $this->laradmin->assetManager->registerBodyClass('main-nav-no-border-bottom');
                return view($this->laradmin->theme->from.'wp.unauthorised');
            }
        }
        return null;
    }

    /**
     * Perform general settings especially based on post custom fields
     *
     * @param Post $post
     * @return void
     */
    private function presets(Post $post){
        //Set container type
        if(str_is(strtolower(trim($post->meta->wide_screen)),'on')){
            $this->laradmin->assetManager->setContainerType('fluid',true);
        }

         // Remove border-bottom on major nav
        if($post->meta->scheme or !str_is(strtolower($post->meta->main_nav_border_bottom),'off')){
            $this->laradmin->assetManager->registerBodyClass('main-nav-no-border-bottom'); 
        }

        
        

        // Main nav scheme
        $main_nav_scheme=$post->meta->main_nav_scheme;
        
        if($main_nav_scheme){
            $this->laradmin->assetManager->registerMainNavScheme($main_nav_scheme);
        }
        
    }

    /**
     * Returns an array of meta items
     *
     * @param Post $post
     * @param string $post_url The url of te post
     * @return array
     */
    private function makeMeta(Post $post,$post_url=null){
        $metas['url'] = $post_url?:route('page', $post->slug);
        $metas['type'] = 'article';
        $metas['title'] = $post->title;
        $metas['description'] = $post->post_excerpt ? $post->post_excerpt : strip_tags(str_limit($post->content, 280,'...'));
        $metas['image'] = $post->image;//TODO: check that this is right
        $metas['tweet'] = str_finish($post->post_excerpt, 277,'...') . ' #' . config('app.name');

        return $metas;
        
    }
    
    /**
     * Custom fields on Wordpress:
     * Instruction:TODO:move custom field to doc when it is all set
     * 
     * Fields                       | Value(s)                              | Description
     * ---------------------------------------------------------------------------------
     * scheme                       | subtle|primary|success|info|...       | The scheme for the page, primarily currently used to style the main section. Any of the brands is valid. the default in most pages is 'default' while it is 'primary' in hero pages
     * minor_nav                    | on|off                                | Turns minor nav ON and OFF
     * minor_nav_scheme             | subtle|primary                        | Determines the class of minor nav
     * blog_listing                 | off|left|right|bottom                 | If not 'off' determines which part of the page shows blog listing. Setting this to 'right' turns ON the rightbar. Setting it to 'left' activates the sidebar unless the sidebar is explicitly off (see sidebar field).
     * blog_listing_count           | [Integer]                             | The maximum number of blog posts to display
     * main_nav_scheme              | subtle|primary                        | Sets the scheme of the main nav
     * main_nav_border_bottom       | on|off                                | Remove the border bottom on main nave when 'off'
     * hero_height                  | dynamic|full|[Integer]                | Determine if the height of the hero should be made to fill the page height or be dynamic relative to content. If integer it will be interpreted as css vh unit.
     * hero_headline_justify        | left|center|right|                    | Horizontal position of the content of a hero
     * hero_content_shade           | on|off                                | Turn the shade for only the content area of the hero ON or OFF. If the scheme custom field is set them the shade will be based on the scheme color. This is unrelated to hero_shade which can shade most of the hero section.
     * hero_content_width           | [Number 0:100%]                       | The width in % of the content area of the hero
     * hero_shade                   | default(default)|angle|smooth|flat    | The shade the help things on the hero like the menu to be seen especially when the hero image is not very dark
     * hero_headline_shade          | on|off                                | When 'on' adds extra shade behind hero content to make it easier to see. Note this is different from section overlay; The default section overlay might already make it easy to see the content.
     * sidebar                      | on|off                                | Allows for the sidebar to be explicitly set to 'on' or 'off'. This field has no effect on page_templates that explicitly include the sidebar (e.g. with_sidebar.blade.php)
     * rightbar                     | on|off                                | Enable or disable the right bar
     * wide_screen                  | on|off(default)                       | When 'on' bootstraps 'container' is replaced with 'container-fluid'
     * hero_fullscreen              | on|off(default)                       | Makes the hero image full screen     
     * hero_headline_align          | top|middle|bottom                     | Used to vertically position the headline inside the hero NOTE: you may need to use numeric hero_height to create the enough vertical height for this setting to have effect
     * hero_type                    | super|hero(default)                   | Determines the type of hero. Super hero extends to the top nav. For a Larus post, setting this field is enough for the post to be considered a hero.
     * social_share_top             | on|off                                | Turn on or off social share at page top
     * social_share_bottom          | on|off                                | Turn on or off social share at page bottom
     * rightbars                    | [String]                              | Comma separated list of page_part slugs whose content should be included in the rightbar. When defined the default rightbar must be included in the list for it to be displayed.
     * sidebars                     | [String]                              | Comma separated list of page_part slugs whose content should be included in the sidebar. Setting this field activates the sidebar unless the sidebar is explicitly set to off (see sidebar field).
     * footers                      | [String]                              | Comma separated list of page_part slugs whose content should be included in the footer.
     * linear_gradient_brand2       | primary|success|info|...              | A brand name to use to make gradient with the current scheme
     * linear_gradient_direction    | [top,left top, left, ...]             | Any of the CSS linear gradient function direction i.e linear-gradient(to left top,), So e.g {left top, top, right bottom, ...}.
     * linear_gradient_fainted      | [Integer] {1,2,3 ...100}              | The faint level of the colors used for gradient. Higher value equals more opaque=>less faint.
     * 
     * Keys: ? => not implemented
     * 
     * Shows a page
     *
     * @param string $slug Page slug
     * @return \Illuminate\Http\Response
     */
    public function page($slug)
    {   
        // Get page
        $post = Page::published()->where('post_name', $slug)->first();

        if(!$post){
            abort(404,'The page you are looking for was not found');
        }
        
        // First check if post needs authentication/authorisation
        if(config('laradmin.wp_page_auth') ){
            $res=$this->checkBefore($post);
            if($res){
                return $res;
            }
        }


        // Make settings
        $post_settings=$this->makeSettings($post);

        // Lets make sidebar white
        $this->laradmin->assetManager->registerBodyClass('sidebar-white');
        

         
        
        $this->presets($post);

        $pageTitle = $post->title;
        
        //// Get the template
        $tpl = $post->meta->_wp_page_template;
        // if (!strlen($tpl) or !file_exists(config('view.paths')[0] . '/vendor/laradmin/user/wp/' . $tpl)) {//NOTE: The corresponding view must be published for this to work, othwerwise the template cannot be found
        //     $tpl = 'page_templates/index.blade.php';
        // }
        $tpl = str_replace('/', '.', $tpl);
        $tpl = str_replace('.blade.php', '', $tpl);
        $tpl=$this->laradmin->theme->from.'wp.' . $tpl;
        //dd($tpl);
        $tpl_default = $this->laradmin->theme->from.'wp.page_templates.index';

        // Define metas;
        $metas=$this->makeMeta($post,route('page', $slug));

        

        

        // Make hero if applicable. 
        $tpl_filename=array_reverse(explode('.',$tpl))[0];
        if(starts_with($tpl_filename,'hero_') or str_is($tpl_filename,'hero')) // we assume  hero if the template starts with 'hero_' or the name is 'hero'     
        {
           $metas['hero']=$this->content2Hero($post);
           $this->laradmin->assetManager->registerHero($post->getHeroImages(),$post->meta->hero_type);
        }
        
        //Get blog posts
        $posts = Post::where(function($query){
            $query->where('post_type', 'post')->orWhere('post_type', 'laradmin_larus_post');
        })->where('post_status', 'publish')->latest()->limit($post->meta->blog_listing_count??4)->get();

        //$is_bs_container_fluid = $this->laradmin->assetManager->isContainerFluid();
         

        return view($this->laradmin->theme->defaultFrom().'wp.page', compact('pageTitle','tpl','tpl_default', 'post', 'metas', 'posts','post_settings'));

    } 

        /**
     * Shows a Larus post
     *
     * @param string $slug Larus post slug
     * @return \Illuminate\Http\Response
     */
    public function larusPost($slug)
    {
       
        // Get Larus post
        $post = LarusPost::published()->where('post_name', $slug)->first();

        if(!$post){
            abort(404,'The page you are looking for was not found');
        }

        // First check if post needs authentication/authorisation
        if(config('laradmin.wp_larus_post_auth')){
            $res=$this->checkBefore($post);
            if($res){
                return $res;
            }
        }
       

        // Lets remove the main menu bottom
        $this->laradmin->assetManager->registerBodyClass('main-nav-no-border-bottom');
        
        // Make settings
        $post_settings=$this->makeSettings($post);

        // Lets make sidebar white
        $this->laradmin->assetManager->registerBodyClass('sidebar-white');
        

        $this->presets($post);

        $pageTitle = $post->title;
        
        $tpl=$this->laradmin->theme->from.'wp.page_templates.index';
        $tpl_default = $this->laradmin->theme->from.'wp.page_templates.index';

        // Define metas;
        $metas=$this->makeMeta($post,route('larus-post', $slug));

        

        // Make hero if applicable. 
        if($post->meta->hero_type){
            $tpl=$this->laradmin->theme->from.'wp.page_templates.hero';
            $metas['hero']=$this->content2Hero($post);
            $this->laradmin->assetManager->registerHero($post->getHeroImages(),$post->meta->hero_type);
        }
        
        //Get blog posts
        $posts = Post::where('post_type', 'laradmin_larus_post')->where('post_status', 'publish')->latest()->limit($post->meta->blog_listing_count??4)->get();
        

        return view($this->laradmin->theme->defaultFrom().'wp.page', compact('pageTitle','tpl','tpl_default', 'post', 'metas', 'posts','post_settings'));

    }

   
    /**
     * Gets basic hero content parts
     *
     * @param Post $post
     * @return array
     */
    private function content2Hero( $post){
        $hero=[
            'title'=>'',
            'btns'=>[],
            'extra'=>'',
            'is_fullscreen'=>false,
        ];
        $content=$post->content;
            
        $dom=new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->loadHTML( $content);


     

        if($h1 = $dom->getElementsByTagName('h1')->item(0)) {
            $hero['title']=$this->getInnerHtml($h1);
            $h1->parentNode->removeChild($h1);
        }

        // First search hero links
        $as=$dom->getElementsByTagName('a') ;
        $hero_as=[];
        foreach($as as $a){
            if($a->hasAttribute('class')){
                if(str_contains($a->getAttribute('class'),'btn-hero')){   
                    $hero_as[]=$a;
                }
            }
        }

        // Now read and properly delete the hero links from the dom.
        $n=count($hero_as);
        for($i=0;$i<$n;$i++){
            $hero['btns'][]=$a->ownerDocument->saveHTML($hero_as[$i]);
            $hero_as[$i]->parentNode->removeChild($hero_as[$i]);
        }
        unset($hero_as);


        // Get the rest of the dom
        $body=$dom->getElementsByTagName('body')->item(0);
        if($body)$hero['extra']=$this->getInnerHTML($body);
        $hero['extra']=str_replace(array("\r",'&#13;', "\n"), '', $hero['extra']);
        //dd($hero['extra']);

        // Check if is fullscreen
        if($post->meta->hero_fullscreen and str_is(strtolower(trim($post->meta->hero_fullscreen)),'on') 
            or str_contains($post->meta->hero_type,'super') ){
            $hero['is_fullscreen']=true;
        }

        return $hero;
    }

    /**
     * Creates a menu under the name breadcrumb. This menu can later be rendered as breadcrumb. 
     * The tag for the menu 
     *
     * @param Post $post
     * @return string
     */
    private function makeBreadcrumb(Post $post){
        $parent = $post->parent;
        
        $tag='breadcrumb';

        $this->laradmin->navigation->create('Home', 'home', $tag, [
            'url' => '/',
        ]);
        while($parent){
            $this->laradmin->navigation->create(str_limit($parent->title,10,'...'), $parent->post_name, $tag, [
                'namedRoute' => 'page',
                'namedRouteParams' => $parent->post_name,
            ]);
            $parent = $parent->parent;
        }
        $this->laradmin->navigation->create(str_limit($post->title,10,'...'), 'current_page', $tag);
        
        return $tag;


    }

  

    /**
     * Add menu for post family
     *
     * @param Post $page
     * @param boolean $levels When true full parent and child menu structure will be created
     * @return void
     */
    private function makePageFamilyMenu(Post $page, $levels = false)
    {
        // Get children
        $children = $page->children()->where('post_type', 'page')->get();

        //Get and add the parent to menu
        $parent = $page->parent;
        $p_mi_tag = '';
        if ($parent) {
            $this->laradmin->navigation->create($parent->title, $parent->post_name, 'page_family', [
                //'iconClass' => 'fas fa-circle',
                'namedRoute' => 'page',
                'namedRouteParams' => $parent->post_name,
                'cssClass' => 'title',
                'htmlBefore' => '<strong>',
                'htmlAfter' => '</strong>'
            ]);
            if ($levels) $p_mi_tag = $parent->post_name;
        }

        // Add the page itself
        if ($children or $parent) {
            $this->laradmin->navigation->create($page->title, $page->post_name, rtrim('page_family.' . $p_mi_tag, '.'), [
                //'iconClass' => 'fas fa-angle-right',
                'namedRoute' => 'page',
                'namedRouteParams' => $page->post_name
            ]);

        }

        $page_mi_tag = str_replace('..', '.', 'page_family.' . $p_mi_tag . '.' . $page->post_name);
        
        

        // Add children
        foreach ($children as $child) {
            //dd($page->post_name);
            $this->laradmin->navigation->create($child->title, $child->post_name, $page_mi_tag, [
                //'iconClass' => 'fas fa-minus',
                'namedRoute' => 'page',
                'namedRouteParams' => $child->post_name,
            ]);
        }
    }

    /**
     * Helps to create settings based on custom fields etc
     *
     * @param Post $post
     * @return array
     */
    public function makeSettings(Post $post){
        // Is sidebar enabled
        $post_settings['has_sidebar']=false;
        if(!str_contains(strtolower($post->meta->sidebar),'off') and ($post->meta->sidebars or str_contains(strtolower($post->meta->blog_listing),'left'))){
            $post_settings['has_sidebar']=true;
        }

        // Is blog listing bottom enabled
        $post_settings['has_bottom_blog_listing']=false;
        if(str_contains(strtolower($post->meta->blog_listing),'bottom')){
            $post_settings['has_bottom_blog_listing']=true;
        }

        // Is the right bar on?
        $post_settings['has_rightbar']=false;
        if(str_contains(strtolower($post->meta->rightbar),'on') or str_contains(strtolower($post->meta->blog_listing),'right')) {
            $post_settings['has_rightbar']=true;
        }

        //Make page family/related menu
        $this->makePageFamilyMenu($post);
        $post_settings['has_page_family'] = !$this->laradmin->navigation->isEmpty('page_family');
    
        // Make breadcrumb
        $post_settings['breadcrumb_tag']=$this->makeBreadcrumb($post);

        return $post_settings;
    }

    /**
     * Retreives inner html of a node
     * 
     * TODO: This Method should be move out of this class into a more general class.
     *
     * @param \DOMNode $node
     * @return void
     */
    function getInnerHtml(\DOMNode $node ) { 
        $innerHTML= ''; 
        $children = $node->childNodes; 
        foreach ($children as $child) { 
            $innerHTML .= $child->ownerDocument->saveXML( $child,LIBXML_NOEMPTYTAG); 
        } 
    
        return $innerHTML; 
    }

    /**
     * Show post
     *
     * @return \Illuminate\Http\Response
     */
    public function post($slug)
    {

        if(!Option::get('wp_blogpost_on_laravel')){
            return redirect()->route('wp', $slug);
        }
        
        
        // Get post
        $post = Post::published()->where('post_name', $slug)->first();

        if(!$post){
            abort(404,'The page you are looking for was not found');
        }


        // Lets remove the main menu bottom
        $this->laradmin->assetManager->registerBodyClass('main-nav-no-border-bottom');

        // Make settings
        $post_settings=$this->makeSettings($post);

        
        // Lets make sidebar white
        $this->laradmin->assetManager->registerBodyClass('sidebar-white');

        $this->presets($post);

        $pageTitle = $post->title;
        
        $tpl=$this->laradmin->theme->from.'wp.page_templates.index';
        $tpl_default = $this->laradmin->theme->from.'wp.page_templates.index';

        // Define metas;
        $metas=$this->makeMeta($post,route('post', $slug));

        

        // Make hero if applicable. 
        if($post->meta->hero_type){
            $tpl=$this->laradmin->theme->from.'wp.page_templates.hero';
            $metas['hero']=$this->content2Hero($post);
            $this->laradmin->assetManager->registerHero($post->getHeroImages(),$post->meta->hero_type);
        }
        
        //Get blog posts
        $posts = Post::where('post_type', 'post')->where('post_status', 'publish')->latest()->limit($post->meta->blog_listing_count??4)->get();
        

        return view($this->laradmin->theme->defaultFrom().'wp.page', compact('pageTitle','tpl','tpl_default', 'post', 'metas', 'posts','post_settings'));
    }


}