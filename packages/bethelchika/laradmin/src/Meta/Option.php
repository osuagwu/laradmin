<?php

namespace BethelChika\Laradmin\Meta;

use BethelChika\Laradmin\Meta\Models\Option as MetaOption;

class Option
{
    /**

     * Add or update the value of the `Meta` at a given key.

     *

     * @param string $name
     * @param mixed $value
     * @return void
     */
    public static function add($name, $value)
    {
        $meta=MetaOption::where('name', $name)->first();
        if (!$meta) {
            $meta=new MetaOption;
        }
        
        $meta->name=$name;
        $meta->value=$value;
        $meta->save();
    }

     /**

     * Add or update the value of the `Meta` at a given key.

     *

     * @param string $name
     * @return void
     */
    public static function remove($name)
    {
        MetaOption::where('name', $name)->delete();
    }

    /**
     * Return a meta value
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function get($name,$default=null){
        return MetaOption::where('name', $name)->first()->value??$default;
    }
}