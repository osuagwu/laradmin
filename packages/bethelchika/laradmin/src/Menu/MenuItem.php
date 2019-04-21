<?php
namespace BethelChika\Laradmin\Menu;

use Illuminate\Routing\Route;




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
            if(strpos($this->url,'http://')!==0 and strpos($this->url,'https://')!==0 and strpos($this->url,'ftp://')!==0 and strpos($this->url,'#')!==0)  {// Note that if the protocol is a protocols other tha http, https or ftp then this will currupt the link
                return rtrim(url(''),'/').'/'.ltrim($this->url,'/');
            }else{
                return $this->url;
                
            }
        }
        elseif ($this->namedRoute) {
            return route($this->namedRoute,$this->namedRouteParams);// TODO: the route function depends on laravel, needs to remove the namedRoute if we want to work independent of laravel
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
     * Check if the link is external
     *
     * @return boolean
     */
    public function isExternalLink(){
        $link=$this->getLink();
        return (strpos($link,'http://')===0 or strpos($link,'https://')===0 or strpos($link,'ftp://')===0)
                and !str_contains($link,env('APP_URL'));
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

        if ($url) {
            $isactive = !strcmp($item->getLink(), $url);//TODO: untested
        } else {
            if($item->hasLink()){
                //if($item->url){
                    
                    $isactive=!strcmp(rtrim(request()->url(),'/'),rtrim($item->getLink(),'/'));
                //}else{
                //    $isactive = !strcmp(request()->url(),route($item->namedRoute));//TODO:://note working
                //}
            }
            
        }

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

}




