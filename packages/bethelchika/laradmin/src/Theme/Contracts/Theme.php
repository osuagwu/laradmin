<?php
namespace BethelChika\Laradmin\Theme\Contracts;
abstract class Theme{
    /**
     * The human readable name of the theme
     *
     * @var string
     */
    public $name;

    /**
     * The Laravel view root string where  templates for the theme are located. It will precede
     * the template names when instantiating views. e.g: view($this->from.'wp.index')
     * 
     * Examples
     * For views Loaded in the theme's service provider as:
     * $this->loadViewsFrom($laradminPath.'/resources/views', 'laradmin');
     * 
     * If templates are stores in /views/ then:
     * $from='laradmin::'
     * 
     * If templates are stores in /views/themes/default/
     * $from='laradmin::themes.default.'
     * 
     * For the case where views can be located via Laravel's 'views' folder, e.g views/mytheme/ then:
     * $from='mytheme.'
     * 
     * 
     *
     * @var string
     */
    public $from;

    /**
     * The default from
     *
     * @var string @see self::$from
     */
    protected static $defaultFrom='laradmin::themes.default.';

    /**
     * Get the default 'from'
     *
     * @return string
     */
    public function defaultFrom(){
        return \BethelChika\Laradmin\Theme\DefaultTheme::$defaultFrom;
    }
    
}