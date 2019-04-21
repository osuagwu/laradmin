<?php
namespace BethelChika\Laradmin\Form\Traits;

use Illuminate\Support\Collection;

Trait Autoform{

    /**
     * Stores the names of registered autoforms
     *
     * @var string
     */
    public static $autoformNames=null;

    /**
     * The menu tag for list of auto form
     *
     * @var string
     */
    public static $autoformNavTag='autoforms';

    /**
     * Register the Autoform
     * @param string $pack The pack of auto forms this belongs to
     * @param string $tag The identifier,.
     * @param string $autoform_name tag name of f
     * @return void
     */
    public static function registerAutoform($pack,$tag,$autoform_name){
        
        if(!self::$autoformNames){
            self::$autoformNames=new Collection;
        }

        if(!self::$autoformNames->has($pack)){
            self::$autoformNames[$pack]=new Collection;
        }
        // if(!self::$autoformNames[$pack]->has($tag)){
        //     self::$autoformNames[$pack][$tag]=new Collection;
        // }
        self::$autoformNames[$pack]->put($tag,$autoform_name);
        

    }

    /**
     * Unregister autoform
     * @param string $pack
     * @param string $tag 
     * @return void
     */
    public static function unregisterAutoform($pack,$tag=null){
        if(!self::$autoformNames){
            return;
        }

        if(self::$autoformNames->has($pack)){
            if($tag) {
                if(self::$autoformNames[$pack]->has($tag)){
                    self::$autoformNames[$pack]->forget($tag);
                }
            }
            else{
                self::$autoformNames->forget($pack);
            }
        }
        
    }

    /**
     * Get autoform name
     *
     * @param string $pack
     * @param string $tag
     * @return Autoform
     */
    public static function getAutoformName($pack,$tag){
        if(!self::$autoformNames){
            return null;
        }

        if(!self::$autoformNames->has($pack)){
            return null;
        }
        if(!self::$autoformNames[$pack]->has($tag)){
            return null;
        }
        return self::$autoformNames[$pack][$tag];
    }

        /**
     * Get autoform name
     *
     * @param string $pack
     * @param string $tag
     * @return Collection
     */
    public static function getAutoformNames($pack){
        if(!self::$autoformNames){
            return new Collection;
        }

        if(!self::$autoformNames->has($pack)){
            return null;
        }
        return self::$autoformNames[$pack];
    }

    /**
     * Get autoform
     *
     * @param string $pack
     * @param string $tag
      * @param string $mode The mode that this call is made for: {'index','edit'}
     * @return Autoform
     */
    public static function getAutoform($pack,$tag,$mode){
        $autoform=self::getAutoformName($pack,$tag);
        if($autoform){
            return new $autoform($pack,$tag,$mode);
        }
        return null;
        
    }
      /**
     * Get all autoforms in a given pack
    * @param string $pack
     * @param string $mode The mode that this call is made for: {'index','edit'}
    * @return Collection
    */
    public static function getAutoforms($pack,$mode='index'){ 
        $autoforms=new Collection;
        foreach(self::getAutoformNames($pack) as $tag=> $autoform_name){
            $autoforms[$tag]=self::getAutoform($pack,$tag,$mode);
        }
        return $autoforms;

    }
        /**
     * Gets the link for form
     * @param string $pack
     * @param string $tag
     * @return string
     */
    public static function autoformLink($pack,$tag){
        return route('user-autoform',[$pack,$tag]);
    }
    

    /**
     * Gets the link for editing form
     * @param string $pack
     * @param string $tag
     * @return string
     */
    public static function autoformEditLink($pack,$tag){
        return route('user-autoform-edit',[$pack,$tag]);
    }

    /**
     * The a form pack and returns a menu tag for them
    *
    * @param string $pack
    * @param string $tag
    * @return string The menu tag that can be used to display the menu
    */
    public static function autoformPackToMenu($pack,$tag=null){
        
        $autoforms=new Collection;
        if($tag){
            $autoforms=collect(self::getAutoform($pack,$tag));
        }else{
            $autoforms=self::getAutoforms($pack);
        }
       

        /// Make nav
        $navigation=self::navigation();
        foreach($autoforms as $autoform){
            //Add a menu to the user settings
            $pack_tag=$autoform->getPack().'_'.$autoform->getTag();
            $route=self::autoformLink($autoform->getPack(),$autoform->getTag());
            
            $navigation->create($autoform->getName(),$pack_tag,self::$autoformNavTag,[
            'url'=>$route,'iconClass'=>'fab fa-wpforms']);
        
        }
        return self::$autoformNavTag;
        
    }

    

}
