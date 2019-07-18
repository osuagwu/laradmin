<?php

namespace BethelChika\Laradmin\Http\Controllers\User;

use BethelChika\Laradmin\WP\Models\Page;
use BethelChika\Laradmin\WP\Models\Post;
use BethelChika\Laradmin\Http\Controllers\Controller;
use BethelChika\Laradmin\Laradmin;
use Illuminate\Support\Facades\Auth;
use BethelChika\Laradmin\Http\Controllers\User\Traits\WPHomepage;

class WPController extends Controller
{
    use WPHomepage;

    private $laradmin;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Laradmin $laradmin)
    {
        parent::__construct();
        $this->laradmin = $laradmin;

        
    } 

    
    /**
     * Show page.
     * Instruction:TODO:move instruction to doc
     * Custom fields on Wordpress:
     * 
     * Fields                   | Value(s)                         | Description
     * ---------------------------------------------------------------------------------
     * scheme                   | subtle|primary|success|info|...  | The scheme for the page, primarily currently used to style the main section. Any of the brands is valid. the default in most pages is 'default' while it is 'primary' in hero pages
     * minor_nav                | on|off                           | Turns minor nav ON and OFF
     * minor_nav_scheme         | subtle|primary                   | Determines the class of minor nav
     * blog_listing             | off|left|right|bottom            | If not 'off' determines which part of the page shows blog listing. Setting this to 'right' turns ON the rightbar.
     * blog_listing_count       | [Integer]                        | The maximum number of blog posts to display
     * main_nav_scheme          | subtle|primary                   | Sets the scheme of the main nav
     * hero_height              | dynamic|full|[Integer]           | Determine if the height of the hero should be made to fill the page height or be dynamic relative to content. If integer it will be interpreted as css vh unit.
     * hero_headline_justify    | left|center|right|               | Horizontal position of the content of a hero
     * hero_shade               | default(default)|angle|smooth|flat| The shade the help things on the hero like the menu to be seen especially when the hero image is not very dark
     * hero_headline_shade      | on|off                           | When 'on' adds extra shade behind hero content to make it easier to see. Note this is different from section overlay; The default section overlay might already make it easy to see the content.
     * rightbar                 | on|off                           | Enable or disable the right bar
     * wide_screen              | on|off(default)                  | When 'on' bootstraps 'container' is replaced with 'container-fluid'
     * hero_fullscreen          | on|off(default)                  | Makes the hero image full screen     
     * hero_headline_align      | top|middle|bottom                | Used to verticaly position the headline inside the hero NOTE: you may need to use numeric hero_height to create the enough vertical height for this setting to have effect
     * hero_type                | super|hero(default)              | Determines the type of hero. Super hero extends to the top nav
     * social_share_top         | on|off                           | Turn on or off social share at page top
     * social_share_bottom      | on|off                           | Turn on or off social share at page bottom
     * rightbars                | [String]                         | Comma separated list of page_part slugs whose content should be included in the rightbar. When defined the default rightbar must be included in the list for it to be displayed.
     * sidebars                 | [String]                         | Comma separated list of page_part slugs whose content should be included in the sidebar.
     * footers                  | [String]                         | Comma separated list of page_part slugs whose content should be included in the footer.
     * linear_gradient_brand2   | primary|success|info|...         | A brand name to use to make gradient with the current scheme
     * linear_gradient_direction| [top,left top, left, ...]        | Any of the CSS linear gradient function direction i.e linear-gradient(to left top,), So e.g {left top, top, right bottom, ...}.
     * linear_gradient_fainted  | [Integer] {1,2,3 ...100}         | The faint level of the colors used for gradient. Higher value equals more opaque=>less faint.
     * 
     * Keys: ? => not implemented
     * 
     * @return \Illuminate\Http\Response
     */
    public function page($slug)
    {
        
        

        

        

        // Get page
        $page = Page::published()->where('post_name', $slug)->first();

        // First check if page needs authentication/authorisation
        if(config('laradmin.wp_page_auth') and $page->needsAuth()){
            if(Auth::guest()){
                $this->laradmin->assetManager->registerBodyClass('main-nav-no-border-bottom');
                return view('laradmin::user.wp.needs_auth');
            }
            if(!Auth::user()->can('view',$page)){
                $this->laradmin->assetManager->registerBodyClass('main-nav-no-border-bottom');
                return view('laradmin::user.wp.unauthorised');
            }
        }

         // Lets make sidebar white
        $this->laradmin->assetManager->registerBodyClass('sidebar-white');

        if(!$page){
            abort(404,'The page you are looking for was not found');
        }
        
        //Set container type
        if(str_is(strtolower(trim($page->meta->wide_screen)),'on')){
            $this->laradmin->assetManager->setContainerType('fluid',true);
        }


        // Remove border-bottom on major nav only if minor nav is ON
        //if(!str_is(strtolower($page->meta->minor_nav),'off')){
           // $this->laradmin->assetManager->registerBodyClass('main-nav-no-border-bottom'); 
        //}

        $pageTitle = $page->title;

        // Main nav scheme
        $main_nav_scheme=$page->meta->main_nav_scheme;
        
        if($main_nav_scheme){
            $this->laradmin->assetManager->registerMainNavScheme($main_nav_scheme);
        }
        
        //// Get the template
        $tpl = $page->meta->_wp_page_template;
        // if (!strlen($tpl) or !file_exists(config('view.paths')[0] . '/vendor/laradmin/user/wp/' . $tpl)) {//NOTE: The corresponding view must be published for this to work, othwerwise the template cannot be found
        //     $tpl = 'page_templates/index.blade.php';
        // }
        $tpl = str_replace('/', '.', $tpl);
        $tpl = str_replace('.blade.php', '', $tpl);
        $tpl='laradmin::user.wp.' . $tpl;
        //dd($tpl);
        $tpl_default = 'laradmin::user.wp.page_templates.index';

        // Define metas;
        $metas['url'] = route('page', $slug);
        $metas['type'] = 'article';
        $metas['title'] = $page->title;
        $metas['description'] = $page->post_excerpt ? $page->post_excerpt : strip_tags(str_limit($page->content, 280,'...'));
        $metas['image'] = $page->image;//TODO: check that this is right
        $metas['tweet'] = str_finish($page->post_excerpt, 277,'...') . '#' . config('app.name');
        

        //Make page family/related menu
        $this->makePageFamilyMenu($page);
        $has_page_family = $this->laradmin->navigation->isEmpty('page_family');

        // Make hero if applicable. 
        $tpl_filename=array_reverse(explode('.',$tpl))[0];
        if(starts_with($tpl_filename,'hero_') or str_is($tpl_filename,'hero')) // we assume  hero if the template starts with 'hero_' or the name is 'hero'     
        {
           $metas['hero']=$this->content2Hero($page);
           $this->laradmin->assetManager->registerHero($page->getHeroImages(),$page->meta->hero_type);
        }
        
        //Get blog posts
        $posts = Post::where('post_type', 'post')->where('post_status', 'publish')->latest()->limit($page->meta->blog_listing_count??4)->get();

        //$is_bs_container_fluid = $this->laradmin->assetManager->isContainerFluid();
        

        return view('laradmin::user.wp.page', compact('pageTitle','tpl','tpl_default', 'page', 'metas', 'posts', 'has_page_family'));
    }

    

    /**
     * Gets basic hero content parts
     *
     * @param Page $page
     * @return array
     */
    private function content2Hero( $page){
        $hero=[
            'title'=>'',
            'btns'=>[],
            'extra'=>'',
            'is_fullscreen'=>false,
        ];
        $content=$page->content;
            
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
        if($page->meta->hero_fullscreen and str_is(strtolower(trim($page->meta->hero_fullscreen)),'on') 
            or str_contains($page->meta->hero_type,'super') ){
            $hero['is_fullscreen']=true;
        }

        return $hero;
    }

    /**
     * Add menu for page family
     *
     * @param Page $page
     * @param boolean $levels When true full parent and child menu structure will be created
     * @return void
     */
    private function makePageFamilyMenu(Page $page, $levels = false)
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
     * Retreives inner html of a node
     *
     * @param \DOMNode $node
     * @return void
     */
    function getInnerHtml(\DOMNode $node ) { 
        $innerHTML= ''; 
        $children = $node->childNodes; 
        foreach ($children as $child) { 
            $innerHTML .= $child->ownerDocument->saveXML( $child ); 
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
        return redirect()->route('wp', $slug);
        // $post = Post::published()->where('post_name',$name)->first();
        // //dd($posts);
        // $pageTitle = $post->title;
         

        // return view('laradmin::user.page.index', compact('pageTitle', 'post'));
    }


}