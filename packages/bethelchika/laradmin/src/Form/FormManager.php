<?php

namespace BethelChika\Laradmin\Form;

use Illuminate\Support\Collection;
use BethelChika\Laradmin\Form\Traits\Autoform;
 class FormManager{
     use Autoform;
    /**
     * Holds all the tag-fieldable name pairs registered
     *
     * @var array
     */
    public static  $fieldableNames;

    public static $formableNames;


    public function __construct(){
        self::$fieldableNames=new Collection;
    }

    /**
     * Register the fiedable
     * @param string $pack The pack where the form for this fieldable is located is located
     * @param string $fieldable_name tag name of fieldable
     * @param string $tag The identifier,.
     * @return void
     */
    public static function registerFieldable($pack,$tag,$fieldable_name){
        if(!self::$fieldableNames->has($pack)){
            self::$fieldableNames[$pack]=new Collection;
        }
        if(!self::$fieldableNames[$pack]->has($tag)){
            self::$fieldableNames[$pack][$tag]=new Collection;
        }
        self::$fieldableNames[$pack][$tag]->push($fieldable_name);
        
        // self::$fieldableNames[$tag][]=$fieldable_name;
        //dd(self::$fieldableNames);
    }

    /**
     * Unregister the fieldable/s. If only pack is given or non-null then the entire pack is deleted. if only fieldable names is not specified then the entire tag is deleted. If all params are given then the fieldable is deleted
     * @param string $pack 
     * @param string $tag 
     * @param string $fieldable_name 
     * @return void
     */
    public static function unregisterFieldable($pack,$tag=null,$fieldable_name=null){
        if(self::$fieldableNames->has($pack)){
            if($tag) {
                if(self::$fieldableNames[$pack]->has($tag)){
                    if($fieldable_name){
                        $key=self::$fieldableNames[$pack][$tag]->serach($fieldable_name);
                        if($key){
                            self::$fieldableNames[$pack][$tag]->forget($key);
                        }
                    }
                    else{
                        self::$fieldableNames[$pack]->forget($tag);
                    }
                    
                }
            }
            else{
                self::$fieldableNames->forget($pack);
            }
        }
    }

    /**
     * Return all or only specifed fieldable names registered. To return an entire pack, provide only the pack name
     * @param $pack
     * @param string $tag Identifier 
     * @return Collection of fieldable names OR a collection of collection of fieldables
     */
    public static function getFieldableNames($pack=null,$tag=null){
        if(!$pack){
            return self::$fieldableNames;
        }

        if(!$tag and self::$fieldableNames and  self::$fieldableNames->has($pack)){
            return self::$fieldableNames[$pack];
        }

        if(self::$fieldableNames and self::$fieldableNames->has($pack) and self::$fieldableNames[$pack]->has($tag)){
            return self::$fieldableNames[$pack][$tag];
        }else return new Collection;
        
    }

    /**
     * Return fieldables registered, in form of Fieldable object, if only pack is given then a collection of collection is returned
     * @param string $pack
     * @param string $tag 
     * @return Collection of Fieldable
     */
    public static function getFieldables($pack,$tag=null){
        $fieldables=self::getFieldableNames($pack,$tag);
        $fos=new Collection;
        if($tag){
            
            foreach ($fieldables as $fn){
                $fos->push(new $fn);
            }
        }else{
            foreach ( $fieldables as $tg=>$fns){
                
                $fos_temp=new collection;
                foreach ($fns as $fn){
                    $fos_temp->push(new $fn);
                }
                $fos->put($tg,$fos_temp);
                $fos_temp=null;
            }
            
        }
        return $fos;
    }

    // /**
    //  * Returns a fieldset
    //  * @param string $tag The form tag to which the fieldset of interest is registered. 
    //  * @param string $name The name of the fieldset
    //  * @return Fieldset|null Null if fieldset could not be found
    //  */
    // public static function getRegisteredFieldset($tag,$name){
    //     ////////////////////////////////////////////
    //     $fieldables=self::getFieldables($tag);
    //     $fields=new Collection;
    //     foreach($fieldables as $fieldable){
    //         foreach($fieldable->getFields() as $field){
    //             switch($field->type){
    //                 case FormItem::FIELDSET://Note that we can more traditionally check the class is fieldset hre if we prefer
    //                     if(str_is($field->name,$name)){
    //                         return $field;
    //                     }
    //                 default:
    //                     continue;
                        
                    
    //             }
    //         }
    //     }
    //     return null;
    // }
//    /**
//      * Returns a field
//      * @param string $tag The form tag to which the field of interest is registered. 
//      * @param string $name The name of the field
//      * @return Field|null Null if field could not be found
//      */
//     public static function getRegisteredField($tag,$name){
//         ////////////////////////////////////////////
//         $fieldables=self::getFieldables($tag);
//         $fields=new Collection;
//         foreach($fieldables as $fieldable){
//             foreach($fieldable->getFields() as $field){
//                 switch($field->type){
//                     case FormItem::FIELDSET://Note that we can more traditionally check the class is fieldset hre if we prefer
//                         continue;
//                     default:
//                         if(str_is($field->name,$name)){
//                             return $field;
//                         }
                    
//                 }
//             }
//         }
//         return null;
//     }

    /**
     * Returns a collection of fields/fieldset
     * @param $pack
     * @param string $tag The form tag to which the field/s of interest is registered
     * @param string $mode The mode that this call is made for: {'index','edit'}
     * @return Collection
     */
    public static function getRegisteredFields($pack,$tag,$mode){
        
        ////////////////////////////////////////////
        $fieldables=self::getFieldables($pack,$tag);
        $fields=new Collection;
        foreach($fieldables as $fieldable){
            $fields=$fields->merge($fieldable->all($pack,$tag,$mode));
           
        }
        return $fields;
    }

  /**
     * Returns a navigation manager
     *
     * @return \BethelChika\Laradmin\Menu\Navigation
     */
    public static function navigation(){
        return app('laradmin')->navigation;
    }
     
}