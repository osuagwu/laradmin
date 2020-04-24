<?php
namespace BethelChika\Laradmin\Menu;


use Illuminate\Support\Facades\Route;

class MenuItem extends NavigationItem
{
    /**
     * Id of the menu item's parent.
     *
     * @var string
     */
    protected $parentId;


    /**
     * The menu item's named route
     *
     * @var string
     */
    public $namedRoute = 'home';

     /**
     * The menu item's named route parameters
     *
     * @var array
     */
     public $namedRouteParams = [];
    /**
     * The menu item's url
     *
     * @var string
     */
    public $url;

    /**
     * The menu item's url query
     *
     * @var string
     */
    public $urlQuery='';
    
    /**
     * The menu item's url fragment
     *
     * @var string
     */
    public $urlFragment='';

    /**
     * The order of the item. The intial value here is set arbitrarily 
     * large so items without explicit order set will be sorted last.
     *
     * @var float
     */
    public $order=100;

    /**
     * The menu item's class for icon with font-awesome for example. Example ='fas fa-home'
     *
     * @var string
     */
    public $iconClass;

    /**
     * The menu item's image
     *
     * @var string
     */
    public $iconImage;

    /**
     * Item is activated if the current url starts with the link of the item
     *
     * @var boolean
     */
    public $activateStartWith=false;

    /**
     * Specifies a dummy item
     *
     * @var boolean
     */
    public $isDummy=false;

    /**
     * List of routes that are virtually connected to the the item. The item is activated whenever any of the routes are active
     *
     * @var array
     */
    public $dummyNamedRoutes=[];

    /**
     * Contruct item
     *
     * @param string $name
     * @param string $tag
     * @param string $namedroute
     */
    public function __construct($name, $tag, $namedroute = null)
    {
        parent::__construct($name, $tag);
        if ($namedroute) {
            $this->namedRoute = $namedroute;
        }

    }

    // /**
    //  * Add an item  to the menu
    //  *
    //  * @param MenuItem $menuitem
    //  * @return mixed Null if the menu, false if there is error and true on success
    //  */
    // public function addCild(MenuItem $menuitem){
    //     $tag=$menuitem->tag;
    //     if($tag){
    //         if($this->getChildByTag($tag)){
    //             return null;
    //         }else{
    //             $this->children[$tag]=$menuitem;
    //         }
    //     }
    //     else{
    //         $this->children[]=$menuitem;
    //     }
        
    //     //Now assign parent id
    //     $menuitem->parentId=$this->id;
    //     return true;
    // }


    /**
     * Add a child to this menu item
     *
     * @param Menuitem $menuitem
     * @param string $tags The tag relative to this menu item where the child should be added. If null the child is added to this menu item
     * @return @see addChild
     */
    public function addChildByTag(MenuItem $menuitem, $tags = null)
    {
        if (!$tags) {
            return $this->addChild($menuitem);
        }

        $tags = explode('.', $tags);
        $tag = array_shift($tags);
        $child = $this->getChildByTag($tag);
        if (count($tags)) {
            if ($child) {
                return $child->addChildByTag($menuitem, implode('.',$tags));
            } else {
                return false;
            }

        } else {
            return $child->addChild($menuitem);
        }

    }




    /**
     * Return parent menu item or null if item is a root menu item, i.e attched to Menu directly
     *
     * @return MenuItem|null
     */
    public function getParent()
    {
        if ($this->parentId) {
            return self::findItem($this->parentId);
        } else {
            return null;
        }
    }

    /**
     * Returns a menu item with the given id
     *
     * @param int $id
     * @return MenuItem|null
     */
    public static function getMenuItemById($id)
    {
        $item = self::findItem($id);
        if ($item instanceof MenuItem) {
            return $item;
        }
        return null;
    }

    /**
     * Check if item has link
     *
     * @inheritdoc
     */
    public function hasLink()
    {
        if ($this->getLink()) {
            return true;
        } else return false;
    }



    /**
     * Get link (without query and fragment strings) of the menu item or return null if no link is defined
     *  TODO: This method depends on Laravel functions url() and route() but it is proabbly for the best;
     * @return mixed
     */
    public function getLink()
    {
        if ($this->url) {
            if(strpos($this->url,'http://')!==0 and strpos($this->url,'https://')!==0 and strpos($this->url,'ftp://')!==0 and strpos($this->url,'#')!==0)  {// Note that if the protocol is  other tha http, https or ftp then this will currupt the link
                return rtrim(url(''),'/').'/'.ltrim($this->url,'/');
            }else{
                return $this->url;
                
            }
        }
        elseif ($this->namedRoute) {
            return route($this->namedRoute,$this->namedRouteParams);//
        } else return null;
    }

    /**
     * Get Full link (including query and fragment strings) of the menu item or return null if no link is defined
     *
     * @return mixed
     */
    public function getFullLink()
    {
        $u=$this->getLink();
        if($this->urlQuery){
            $u=$u.'?'.$this->urlQuery;
        }
        if($this->urlFragment){
            $u=$u.'#'.$this->urlFragment;
        }
        return $u;
    }

   /**
     * Check if the link is external. 
     * TODO: Note that currently subdomains except 'www.' are considered different. This can easily be implemented when required.
     *
     * @return boolean
     */
    public function isExternalLink(){
        $host=parse_url($this->getLink(),PHP_URL_HOST);
        $host=str_replace('www.','',$host);// TODO: Should this be done only if occures at the start?
        if($host){
            $app_host=parse_url(config('app.url'),PHP_URL_HOST);
            $app_host=str_replace('www.','',$app_host);// TODO: Should this be done only if occures at the start?
            return !!strcmp($host,$app_host);
        }
        return false;
        // $link=$this->getLink();
        // $links_no_protocol=str_replace(['http://','https://','ftp://'],'',[$link,env('APP_URL')]);

        // return (strpos($link,'http://')===0 or strpos($link,'https://')===0 or strpos($link,'ftp://')===0)
        //         and !str_contains($links_no_protocol[0],$links_no_protocol[1]);
    }

    /**
     * Check if item has icon
     *
     *@inheritdoc
     */
    public function hasIcon()
    {
        if ($this->getIcon()) {
            return true;
        } else return false;
    }

    /**
     * Get icon of the menu item or return null if no icon is defined
     *
     * @return mixed
     */
    public function getIcon()
    {
        if ($this->iconImage) return $this->iconImage;
        elseif ($this->iconClass) {
            return $this->iconClass;
        } else return null;
    }

    /**
     * 
     *
     * @inheritdoc
     */
    public function hasParent()
    {
        if ($this->getParent()) {
            return true;
        }
        return false;
    }

         /**
     * 
     *
     * @inheritdoc
     */
    public function isMenuItem(){
        return true;
    }
         /**
     * 
     *
     * @inheritdoc
     */
    public function isMenu(){
        return false;
    }

    /**
     * Recursively set an item active. TODO: This method is currently not in use
     *
     * @return void
     */
    public function setActive(){
        $this->active=true;
        self::navigation()->addActiveTags($this->getTags());//keepping all active items in navigation
        if($this->hasParent()){
            $this->getParent()->setActive();
        }
    }

    /**
     * activate a given menu item with a given url. This Method will depend on Laravel if $url is null
     *
     * @param MenuItem $item
     * @param string $url
     * @return void
     */
    public static function activate(MenuItem $item, $url = null)
    {
        $isactive = false;

        $current_url=rtrim(request()->url(),'/');

        if ($url) {
            $isactive = !strcmp($item->getLink(), $url);//TODO: untested
        } else {
            if($item->hasLink()){
                //if($item->url){
                    
                    $isactive=!strcmp($current_url,rtrim($item->getLink(),'/'));
                //}else{
                //    $isactive = !strcmp(request()->url(),route($item->namedRoute));//TODO:://note working
                //}
            }
            
        }



        // Do we need to activate if the current url starts with the link of this 
        // item. This is a trick to activate parents
        if(!$isactive){
            if($item->activateStartWith){
                if ($url) {
                    $isactive = starts_with($url,str_finish($item->getLink(),'/'));//TODO: untested
                } else {
                    if($item->hasLink()){                       
                        $isactive=starts_with($current_url,str_finish($item->getLink(),'/'));
                        //
                        //  if($isactive){
                        // //     if(str_contains($item->getLink(),'plug')){dd($item->getLink());}
                        //      print_r('<div>'.$item->getTag().'</div><b>'.$item->getLink().'</b>');
                        //      dd($item);
                        //  }
                    }
                    
                }
            }
        }



        ///////////////////////////////////////////////////////////////////
        //////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////
        //check if any of the dummy route matches the current route
        if(!$isactive){
            foreach($item->dummyNamedRoutes as $d_route){
                if(!strcmp($d_route,Route::currentRouteName())){
                    $isactive=true;
                    break;
                }
            }
        }

        // special activation for dummy item
        if(!$isactive){
            if($item->isDummy and  $item->namedRoute){//Note that we must always activate by route name if the item is dummy otherwise dummy routes with parameters will not work well since url changes based on parameters while route name stays the same
                $isactive=!strcmp($item->namedRoute,Route::currentRouteName());    
            }
        }
        //////////////////////////////////////////////////////////////
        //////////////////////////////////////////////
        ////////////////////////////////////////////////////////////
        

        if ($isactive) {
            $item->active = true;
            self::navigation()->addActiveTags($item->getTags());//keepping all active items in navigation
            self::makeParentsActive($item);
        }
    }

    /**
     * activate a given menu item with a given url recursively
     *
     * @param MenuItem $item
     * @param string $url
     * @return void
     */
    public static function activates(MenuItem $item, $url = null)
    {

        if($item->hasChildren()){
            foreach($item->getChildren() as $child){
                self::activates($child,$url);
            }
            
        }
        
        self::activate($item,$url);
        
        
    }

    /**
     * Recursively set parents of the given item active
     *
     * @param MenuItem $item
     * @return void
     */
    public static function makeParentsActive(MenuItem $item){
        $parent = $item->getParent();
        if ($parent instanceof MenuItem) {
            $parent->active=true;
            // self::navigation()->addActiveTags($item->getTags());///No need to add parents as their children will be added. keepping all active items in navigation
            if($parent->parentId){
                self::makeParentsActive($parent);
            }
        }
        
    }

    /**
     * Add dummy routes 
     *
     * @param mixed $named_routes Array or comma separated list of route names
     * @return void
     */
    public function addDummyNamedRoutes($named_routes=[]){
        if(!is_array($named_routes)){
            $named_routes=explode(',',$named_routes);
        }
        $this->dummyNamedRoutes=array_unique(array_merge( $this->dummyNamedRoutes,$named_routes));
        
        // for($i=0;$i<count($named_routes);$i++) {
        //     $d=new MenuItem($named_routes[$i],$named_routes[$i],$named_routes[$i]);//use the route name for 'name' and 'tag' since anything unique among the sibilings will work given this is a dummy
        //     $d->isDummy=true;
        //     if(count($route_parameters)>=$i+1){
        //         $d->namedRouteParams=$route_parameters[$i];
        //     }
        //     $this->addChild($d);
        // }
        //* @param array $route_parameters The entries must correspond to that of $named_routes but can be less inwhich case later values of $named routes ar assumed to have no parameters. to $named_routes. Note that each route parameter is an array so this is array of arrays
    }
        

}




