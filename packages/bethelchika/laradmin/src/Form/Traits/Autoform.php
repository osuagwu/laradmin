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
     * Register the Autoform
     * @param string $pack The pack of auto forms this belongs to
     * @param string $tag The identifier,.
     * @param string $autoform_name tag name of f
     * @return void
     */
    public static function registerAutoform($pack,$tag,$autoform_name){
        if(!self::$autoformNames);self::$autoformNames=new Collection;//TODO:Could put this in parent class constructor

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
     * @return void
     */
    public function getAutoformName($pack,$tag){
        if(!self::$autoformNames->has($pack)){
            return null;
        }
        if(!self::$autoformNames[$pack]->has($tag)){
            return null;
        }
        return self::$autoformNames[$pack][$tag];
    }

    /**
     * Get autoform
     *
     * @param string $pack
     * @param string $tag
     * @return void
     */
    public function getAutoform($pack,$tag){
        $autoform=self::getAutoformName($pack,$tag);
        
        return new $autoform($pack,$tag);
    }

}
