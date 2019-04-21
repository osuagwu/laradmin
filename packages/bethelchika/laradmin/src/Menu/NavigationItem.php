<?php
namespace BethelChika\Laradmin\Menu;

use Illuminate\Support\Collection;


abstract class NavigationItem
{
    /**
     * The last auto generated identifier
     *
     * @var int
     */
    protected static $AUTO_INCREAMENT;

    /**
     * The menu identifier
     *
     * @var int
     */
    protected $id;

    /**
     * Name of the item
     *
     * @var string
     */
    public $name;

    /**
     * Tag of the item. The tag cannot be changed. The tag can form a path with parent tags similar to filepath which is unique for each
     *
     * @var string
     */
    private $tag;



    /**
     * Is the item active 
     *
     * @var boolean
     */
    public $active = false;

    /**
     * The item children
     *
     * @var array
     */
    private $children = [];

    /**
     * Item's html before.
     *
     * @var string
     */
    public $htmlBefore;
    /**
     * Item's html after.
     *
     * @var string
     */
    public $htmlAfter;

    /**
     * The  item's array of html attributes in key-value pair i.e the key is html attribute while value is the corresponding value
     * 
     * @var array
     */
    public $htmlAttributes = array();
/**
     * The item style classes
     *
     * @var string
     */
    public $cssClass;
 
    
    /**
     * The ARIA role of the item in html
     *
     * @var string
     */
    public $ariaRole='presentation';
    
    /**
     * The item javascript <script>print $js</script>
     *
     * @var string
     */
    public $js;

    

    /**
     * The item display state, 1=hidden from authenticated users only, 2=hidden from guests only | 3=hidden from all | 0=not hidden for all 
     *
     * @var integer
     */

    public $hidden=0;

    /**
     * Adds some description to menu item 
     *
     * @var string
     */

    public $comment=null;

    
    

    public function __construct($name, $tag)
    {


        $this->name = $name;
            
            //assign unique id
        self::$AUTO_INCREAMENT++;
        $this->id = self::$AUTO_INCREAMENT;

        $this->tag = $tag;

        // if ($tag) {
        //     $this->tag = $tag;
        // } else {
        //     $this->tag = $this->id;
        // }

    }
    /**
     * Set the properties dynamically using the array key value pair where array key is te property to set
     *
     * @param array $array
     * @return void
     */
    function set(array $array) {
        $refl = new \ReflectionClass($this);
        //$public_properties = $refl->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach ($array as $propertyToSet => $value) {

            try{// we try because property may not exist
                $property = $refl->getProperty($propertyToSet);
            }catch(\Exception $ex){
                continue;
            }
      
          if ($property instanceof \ReflectionProperty) {
            if(!$property->isPublic()){
                continue;
            }
            $property->setValue($this, $value);
          }
        }
      }
    //   function set($array){
    //     foreach ($array as $key => $value){
    //         if ( property_exists ( $this , $key ) ){
    //             $this->$key = $value;
    //         }
    //     }
    // }
    /**
     * Generates and returns the next autoincreament number
     *
     * @return void
     */
    private static function getNextId(){
        return ++self::$AUTO_INCREAMENT;
        
    }

    /**
     * Set/Reset IDs for Navigation items
     *
     * @param NavigationItem $item
     * @param NavigationItem $parent
     * @return void
     */
    public static function setIds(NavigationItem $item=null,NavigationItem $parent=null){
        
        //assign fresh IDs to the menus
        // if($item){
        //     $items=$item;
        // }else{
        //     $items= self::navigation()->getMenus();;
        // }
        //foreach($items as $item){
            //dd($item);
            $item->id=self::getNextId();
            if($parent){
                $item->parentId=$parent->id;
            }
            foreach($item->getChildren() as $child){
                self::setIds($child,$item);
            }
        //}
    }

    /**
     * Add an item  to the NavigationItem
     *
     * @param NavigationItem $item
     * @return mixed Null if the item with a given tag exists in this NavigationItem, true on success
     */
    public function addChild(NavigationItem $item)
    {
        $tag = $item->tag;
        
        if ($this->getChildByTag($tag)) {
            return null;
        } else {
            $this->children[$tag] = $item;
        }
        
        //Now assign parent id
        $item->parentId = $this->id;
        return true;
    }
    /**
     * Find a NavigationItem with a given id
     *
     * @param int $id
     * @param NavigationItem $item
     * @return @see findItemIn
     */
    public static function findItem($id)
    {
        $menus = self::navigation()->getMenus();
        foreach ($menus as $menu) {
            $item = self::findItemIn($id, $menu);
            if ($item) {
                return $item;
            }
        }
        return false;
    }

    /**
     * Find a NavigationItem with a given id within  the given NavigationItem
     *
     * @param int $id
     * @param NavigationItem $item
     * @return NavigationItem
     */
    public static function findItemIn($id, NavigationItem $item)
    {
        if ($item->id == $id) {
            return $item;
        } else {
            foreach ($item->getChildren() as $child) {
                $item = self::findItemIn($id, $child);
                if ($item) {
                    return $item;
                }
                //else{
                //     foreach($item->getChildren() as $child2){
                //         $item2=findItemIn($id,$child);
                //         if($item2){
                //             return $item2;
                //         }
                //     }
                // }
            }
        }
        return false;
    }


    /**
     * Return the tag of this itme
     *
     * @return string
     */
    public function getTag(){
        return $this->tag;
    }

    /**
     * Return the dot separated tags down to the parent menu of this item
     *
     * @return string
     */
    public function getTags(){
        $tags[]=$this->tag;
        $parent=$this->getParent();
        while($parent){
            $tags[]=$parent->getTag();
            $parent=$parent->getParent();
            
        }
        return implode('.',array_reverse($tags));
    }

    /**
     * Checks if this item has a chiled with a given tag and returns the child or false otherwise
     *
     * @param string $tags A tag or dot separated tags each suitable for array indexing
     * @return NavigationItem | boolean
     */
    public function getChildByTag($tags){
        $tags=explode('.',$tags);
        $tag=array_shift($tags);
        foreach($this->getChildren() as $child){
            if(!strcmp($child->tag,$tag)){
                if(count($tags)){
                    return $child->getChildByTag(implode('.',$tags));
                }else{
                    return $child;
                }
            }
        }
        return false;

    }

    /**
     * Remove an item  with a given tag or dot separated tag
     *
     * @param string $tags One or dot saparated tag identifier. Dot separated tag should be relative to the tag of the parent i.e. this object 
     * @return boolean true on success or false if menu does not exist , otherwise false
     */
    public function removeChildByTag($tags){
        
        $tags=explode('.',$tags);
        $tag=array_shift($tags);
        $child=$this->getChildByTag($tag);
        if(count($tags)){
            if($child){
                return $child->removeChildByTag($tags);
            }else{
                return false;
            }
            
        }else{
            if($child){
                return $this->removeChild($child);
            }else{
                return false;
            }
        }
    }

    /**
     * Remove a given child
     *
     * @param string $tag
     * @return boolean, true on success or if the menu item could not be found
     */
    public function removeChild(NavigationItem $item){
        
        $child=$this->findChild($item->id);
        if($item->cmp($child)){
            unset($this->children[$item->tag]);
        }
        
        return true;
    }
    /**
     * Check if this item has children
     *
     * @return boolean
     */
    public function hasChildren(){
        return count($this->children)>0;
    }

     /**
     * Check if item has link
     *
     * @return boolean
     */
    public abstract function hasLink();

     /**
     * Check if item has icon. 
     *
     * @return boolean 
     */
    public abstract function hasIcon();

     /**
     * Check if item has a parent. 
     *
     * @return boolean . 
     */
    public abstract function hasParent();

    /**
     * Get parent item. 
     *
     * @return NavigationItem . 
     */
    public abstract function getParent();

     /**
     * Check if item is a child of menu. 
     *
     * @return boolean . 
     */
    public abstract function isMenuItem();
    /**
     * Check if item is a menu. 
     *
     * @return boolean . 
     */
    public abstract function isMenu();

    /**
     * Find among childred the given id
     *
     * @param int $id
     * @return NavigationItem|null
     */
    public function findChild($id){
        foreach($this->getChildren() as $child){
            if($child->id==$id){
                return $child;
            }
        }
        return null;
    }

    
    /**
     * Check if item is active. 
     *
     * @return boolean 
     */
     public function isActive(){
         return $this->active;
     }


    /**
     * Compares this items with a given item and returns true if they are the same
     *
     * @param NavigationItem $item
     * @return boolean
     */
    public function cmp(NavigationItem $item){
        return $this->id==$item->id;
    }
   
    /**
     * Return children
     *
     * @return array NavigationItem
     */
    public function getChildren(){
        //return $this->children;
        return (new Collection($this->children))->sortBy('order')->all(); //TODO: should sort without Laravel Collection to keep dependency low
    }

    // public static function sort(Collection $items){

    // }

    /**
     * Returns the html attributes formated. 
     *
     * @return string
     */
    public function getHtmlAttributes(){
        $attrs=[];
        foreach($this->htmlAttributes as $key=>$val){
            $attrs[]=$key.'="'.$val. '"';
        }
        return implode(' ',$attrs);
    }

    

    /**
     * Checks if this item can be shown to all, auth or guest users
     * TODO: (ref:TODO-ISHIDEN-ANCESTORS in menu.blade.php) check if it is useful (may not be disirable) to also check ancestors of the item here anf if they are hidden, claim that this item is also hidden regardless of its own setting. .
     * @param string $who all|guest|auth
     * @return boolean
     */
    public function isHidden($who='all'){
        switch(strtolower($who)){
            case 'all':
                return $this->hidden==3;
                break;
            case 'guest':
                return $this->hidden==2;
                break;
            case 'auth':
                return $this->hidden==1;
                break;
            default:
                return true;
        }
        
    }

    /**
     * Returns the navigation object. Note this depends on laravel
     *
     * @return Navigation
     */
    protected static function navigation(){
        return app('laradmin')->navigation;//TODO: Depends on laravel and laradmin
    }

    
}