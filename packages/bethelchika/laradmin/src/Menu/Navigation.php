<?php
namespace BethelChika\Laradmin\Menu;

class Navigation{

    /**
     * Navigation file
     *
     * @var string
     */
    protected static $navigationFile='navigation.nav'; 

    /**
     * Keep all menus 
     * @var array
     */
    protected static $menus=[];

    /**
     * Tells when the man activation method has been called i.e when surely all the entire navigation system has been activated. So can be used to prevent runing the method all over again.
     *
     * @var boolean
     */
    private static $isActivated=false;
    
    /**
     * Keep all active menu item dot separated tags
     *
     * @var array
     */
    protected static $activeTags=[];

    /**
     * Construct new navigation
     *
     * @param string $navigationFile
     */
    public function __construct($navigationFile=null){
        if($navigationFile){
            self::$navigationFile=$navigationFile;
        }

    }

    /**
     * Initializes items
     * @param $navigationFile
     * @return void
     */
    public static function init($navigationFile=null){
        if($navigationFile){
            self::$navigationFile=$navigationFile;
        }
        // Load items
        if(file_exists(self::$navigationFile)){
            self::$menus=unserialize(file_get_contents(self::$navigationFile));
            if(!is_array(self::$menus)){
                self::$menus=[];
            }
        }

        // Set/reset universal ids for items
        foreach(self::getMenus() as $menu){
            NavigationItem::setIds($menu);
            
        }
        
    }

    /**
     * Add an active item tags 
     *
     * @param string $tags Dot separated tags to uniquely identify and item
     * @return void
     */
    public static function addActiveTags($tags){
        //xxxxxxxxxxxxxxxNOTE that this block is not neccessasry and can be savely be removed if it is causing problem. It tries to avoid adding parents and duplicatesxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
        foreach(self::$activeTags as $at){//
            
            if(starts_with($at,$tags))return;//Basically if the one that is already in starts with the new one we are trying to add, then the new one is a parent and we do not need to add it.
        }
        //xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
        self::$activeTags[]=$tags;
        
    }
   
    /**
     * If it exists, returns first found dot separated active tag which must start with the spicified tag
     *
     * @param string $roottag Tag/dot separated tag that must preceed the returned tags.
     * @return string Null on empty result 
     */
    public function getActiveTag($roottag){

        $at=null;
        foreach(self::$activeTags as $at){
            if(starts_with($at,$roottag)){
                break;//NOTE taht we are only cosidering the first match here.
            }
        }

        return $at;

    }

     /**
     * Returns array of active dot separated tags
     *
     * @return array
     */
    public static function getActiveTags(){
            return self::$activeTags;
    }

    /**
     * Return the item tag that should be rendered for the current minor menu or null if all the parents befor the roottag has no children.
     * Note that this function is based on the current active tag/s
     *
     * @param string $roottag The roottag with which to base the miomor menu
     * @return string
     */
    public function getMinorNavTag($roottag='primary'){

        //First make sure navoigation is activated
        self::activates();//Makes sure that menus are activated else null will be returned

        // Now carry on
        $at=self::getActiveTag($roottag);
        
        if (!$at)return null;
        
        $item=self::getMenuByTags($at);
        if($item){
            while(!$item->hasChildren() and strcmp($roottag,$item->getTags()) ){//keep going untill we find one with children unless we get to the roottag in which case not need to return it as its children should already be listed in the page
                $item=$item->getParent();
            }

            if($item->hasChildren() and strcmp($roottag,$item->getTags() )){//i.e if item has children and it not the same as the root tag
                return $item->getTags();
            }else{
                return null;
            }
        }else{
            return null;
        }
    }
    
    /**
     * Add a menu making sure tag is unique
     *
     * @param Menu $menu
     * @return boolean True on success
     */
     public static function addMenu(Menu $menu){
        //dd($menu->getTag());
        if(self::getMenuByTag($menu->getTag())){
            return false;
        }
        self::$menus[$menu->getTag()] =$menu;
        return true;
     }
     

     /**
      * Removes a menu. TODO: has never ben tested
      *
      * @param Menu $menu
      * @return void
      */
     public static function removeMenu(Menu $menu){
        unset(self::$menus[$menu->tag]);
     }

     /**
      * Returns all menus
      *
      * @return void
      */
    public static function getMenus(){
        return self::$menus;
    }

    /**
     * Returns a menu with the given tag
     *
     * @param string $tag tag must be suitable for array indexing
     * @return Menu
     */
    public static function getMenuByTag($tag){
        $menus=self::getMenus();
        if(isset($menus[$tag])){
            return $menus[$tag];
        }
        return null;
    }

     /**
     * Returns a menu or menuitem with the given tag or dot separated tags respectively.
     *
     * @param string $tags Each tag must be suitable for array indexing. The first tag must be for a Menu.
     * @return NavigationItem or null
     */
    public static function getMenuByTags($tags){
        $tags=explode('.',$tags);
        $menu=self::getMenuByTag($tags[0]);
        if(!$menu or count($tags)==1){
            return $menu;
        }
        array_shift($tags);
        return $menu->getChildByTag(implode('.',$tags));   
    }

    // public static function order(NavigationItem $item){
    //     if (!$item->hasChildren()){
    //         return $item;
    //     }
    //     dd( collect($item));
    // }

    /**
     * Checks if menu is empty
     *
     * @param string $tags Each tag must be suitable for array indexing. The first tag must be for a Menu and it is the only one used.
     * @return boolean Null is returnd if menu does not exists.
     */
    public static function isEmpty($tags){
        $tags=explode('.',$tags);
        $menu=self::getMenuByTag($tags[0]);
        if(!$menu)return null;
        return $menu->hasChildren();
    }

    /**
     * Check if an item has children
     *
     * @param  string $tags Each tag must be suitable for array indexing. The first tag must be for a Menu.
     * @return boolean Null is returnd if item does not exists.
     */
    public static function hasChildren($tags){
        $item=getMenuByTags($tags);
        if(!$item)return null;
        return $item->hasChildren();
    }

     /**
      * Store the navigation
      *
      * @return void
      */
      public static function store(){
          
        file_put_contents(self::$navigationFile,serialize(self::getMenus()));
        
    }


    /**
     * Loads default menu
     *
     * @return @see Menu::factory()
     */
    public static function factory(){
        Menu::factory();
    }

    /**
     * Loads default menu
     *
     * @return @see Menu::factory()
     */
    public static function factoryReset(){
        self::clearAll();
        Menu::factory();
    }

    /** TODO: There an issue withis method when trying activate an empty menu
     * Check the item that should be active and set it so. If no tag is specified, all menu will be activated
     * @param $tags the tag [or dot separated tags] of the menu [or menuItem]
     * @param $url (defualt=null) the url we want to activate against
     * @param boolean $isforced When true, this method is still executed even if it has been previously been executed
     * @return void
     */
    public static function activates($tags=null,$url=null,$isforced=false){
        if(self::$isActivated and !$isforced)return; //return if it has been run before and we are not forcing
        
        //dd($tags);
        if($tags){
            $tags=explode('.',$tags);
            $menu=self::getMenuByTag($tags[0]);
            //vardump($menu);exit();
            if($menu){
                if(count($tags)==1){
                    foreach($menu->getChildren() as $item){
                        MenuItem::activates($item,$url);   
                    }
                }else{
                    array_shift($tags);
                    MenuItem::activates($menu->getChildByTag(implode('.',$tags)));
                }
            }
            return;//We return without making a note that this method has been executed b/c since tags are given, not neccessarily all navigation were activated;
        }
        
        foreach(self::getMenus() as $menu){
            foreach($menu->getChildren() as $item){
                MenuItem::activates($item,$url);
                
            }
            
        }

        self::$isActivated=true;//makes a note that this method has been executed;
        
    }

    /**
     * Render a specified menu
     * @param string $tag
     * @return @see Menu::render()
     */
    public static function render($tag){
        
        Menu::render($tag);
    }

    /**
     * Clear all menu
     * @return void
     */
    public static function clearAll(){
        self::$menus=[];
    }


    /**
     * Puts a given menu item in a specified tag path, $tags which can be dot separated. The first tag must refer to a menu.
     * Example Navigation::put($menuitem,'primary.products') where 'primary' is a Menu and prducts is a MenuItem
     * @param MenuItem $item
     * @param string $tags The dot separated tag path(unless only menu is provided) where the first item tag must refer to a menu and the rest are for menu items.* Example Navigation::put($menuitem,'primary.products') where 'primary' is a Menu and prducts is a MenuItem
     * @return void
     */
    public static function put(MenuItem $item, $tags){
        $tagsx=explode('.',$tags);
        
        $menutag=array_shift($tagsx);
        $menu=self::getMenuByTag($menutag);

        if(count($tagsx)==0){
            $menu->addChild($item);
        }else{   
            $child_tag=array_shift($tagsx);
            $child=$menu->getChildByTag($child_tag);
            
            if(count($tagsx)==0){ //Then add to the current
                $child->addChild($item);
            }else{
                $child->addChildByTag($item,implode('.',$tagsx));
            }
        }

    }

/**
     * First make a MenuItem with the given name and tag. The @see Navigation::put
     * Example Navigation::put('Shoes','shoes','primary.products') where 'primary' is a Menu and prducts is a MenuItem
     *
     * @param string $name Name of the MenuItem that will be made
     * @param string $tag Tag of the Menu Item that will be made
     * @param string $tags @see Navigation::put
     * @return MenuItem
     */
    public static function makeAndPut($name,$tag, $tags){
        $item=new MenuItem($name,$tag);
        self::put($item,$tags);
        return $item;
        
    }


    /**
     * Place the given menu item in a specified tag path
     *
     * @param MenuItem $item
     * @param string $tags Tage path. @see Navigation::put
     * @return void
     */
    public static function putOrAdd(MenuItem $item, $tags){
        $tagsx=explode('.',$tags);
        $menutag=array_shift($tagsx);

        $menu=self::getMenuByTag($menutag);
        if(!$menu){
            $menu=new Menu(ucfirst($menutag),$menutag);
            self::addMenu($menu);
            //return self::putOrAdd($item,$tags);
        }
        //$child_tag=array_shift($tagsx);
        //$child=$menu->getChildByTag($child_tag);

        $child_tags='';
        $c=0;
        foreach($tagsx as $tagx){
            $c=$c+1;
            $tags_temp=trim($child_tags.'.'.$tagx,'.');

            $child_temp=$menu->getChildByTag($tags_temp);
            if(!$child_temp){
                $temp=new MenuItem(ucfirst($tagx),$tagx);
                if($c==1){
                    $menu->addChild($temp);
                }else{
                    $menu->getChildByTag($child_tags)->addChild($temp);
                }   
            }
            $child_tags=$tags_temp;
        }
        
        self::put($item,$tags);

        
    }

    /**
     * Create a menu item specified by name and tag and add it to the given tag path
     *
     * @param string $name
     * @param string $tag the tag of og the menu item
     * @param string $tags @see Navigation::put
     * @param array $vars A key value pair for specifying the properties of the newly created item where the array key is the property to be set 
     * @return void
     */
    public static function makeAndPutOrAdd($name,$tag, $tags,$vars=null){
        $item=new MenuItem($name,$tag);
        self::putOrAdd($item,$tags);
        if($vars){
            $item->set($vars);
        }
        return $item;
    }
    
    /**
     * An easiest way to create a menu item and add it to a menu and  @see Navigation::makeAndPutOrAdd()
     * 
     * E.g. :  
     *  // Navigation::create('Test1','test1','primary');
     *  // Navigation::create('Test2','test2','primary.test1');
     *  // Navigation::create('Test3','test3','primary.test10');
     *  // Navigation::create('Test4','test4','primary.test10.test3');
     *  // Navigation::create('Test5','test5','primary.test10.test3');
     *  // Navigation::create('Test6','test6','primary.test10.test3.test5');
     *  // $item7=Navigation::create('Test7','test7','primary.test10.test3.test5.test6',['url'=>'http://www.bbc.co.uk']);
     *   
     *
     * @param string $name @see Navigation::makeAndPutOrAdd()
     * @param string $tag @see Navigation::makeAndPutOrAdd()
     * @param string $tags @see Navigation::makeAndPutOrAdd(). Default is 'primary'
     * @param array $vars @see Navigation::makeAndPutOrAdd()
     * @return @see Navigation::makeAndPutOrAdd()
     */
    public static function create($name,$tag, $tags=null,$vars=null){
        if(!$tags){
            $tags='primary';
        }
        return self::makeAndPutOrAdd($name,$tag, $tags,$vars);
    }
}