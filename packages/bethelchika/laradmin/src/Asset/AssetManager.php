<?php
namespace BethelChika\Laradmin\Asset;

class AssetManager
{
    /**
     * TODO: when the need arises create styles and scripts. But for now we implement a lazy crude method $assets which quickly allows inclusion of a string as assest so the programmer can include whatever
     *
     * 
     */

    /**
     * The name appended to logo to change its type. e.g logo-{{$logoType}}.svg
     *
     * @var string
     */
    private static $logoType = '';

    /**
     * The navbar class the defines the look and feel of the main navigation
     *
     * @var string Possible values include bootsrap brand classes ={primary,danger,success,warning,info}; although some of these may not be implemented
     */
    private static $mainNavClass = '';

    /**
     * A holistic var for any asset string allowing the programmer to include whatever as asset.
     *
     * @var array
     */
    private static $assets = [];

    /**
     * Predefined stacks.
     *
     * @var array
     */
    public static $stacks = [
        'head-styles',
        'footer-scripts-after-library',
        'footer-scripts',
        'meta',
    ];



    /**
     * Body class
     *
     * @var array
     */
    private static $bodyClasses = [];

    /**
     * Predefined admin stacks.TODO: to be used to implement admin stuff
     *
     * @var array
     */
    public static $adminStacks = [
        'admin-head-styles',
        'admin-footer-scripts-after-library',
        'admin-footer-scripts',
    ];

    /**
     * The container type. [0]=> bootstraps' container which can be fluid or static, [1]=>true if the current type setting is very important and should be changed.
     *
     * @var string
     */
    public static $containerType = ['static', false];

    /**
     * Stores the type of hero. It is null if  no hero is defined.
     *
     * @var string {'hero','hero-super'}
     */
    public static $heroType = null;

    /**
     * Adds an asset
     *
     * @param string $tag A site wise unique id for this asset
     * @param string $string The assest eg. <script ...>, <link ...>, <style ...>. Note: It is up to the programmer to keep this small in size (should consider resgistering only <link> and <script src="..."> for large assests).
     * @param string $stack The stacks (from predefined ones) on the page where the assest should be placed
     * @return string
     */
    public static function registerAsset($stack, $tag, $string)
    {
        self::$assets[$stack][$tag] = ['content' => $string];
    }

    /**
     * Returns asssets for a specified stack
     *
     * @param string $stack
     * @return string
     */
    public static function getAssetsString($stack)
    {
        $strings = '';
        //dd(self::$assets);
        if (array_key_exists($stack, self::$assets)) {
            foreach (self::$assets[$stack] as $tag) {
                $strings .= $tag['content'];
            }
        }

        return $strings;
    }

    /**
     * Returns a specified asset. Also useful for when you want to load from none predefined stacks as those are not loaded automatically
     *
     * @param string $stack
     * @param string $tag
     * @return string
     */
    public static function getAssetString($stack, $tag)
    {
        return self::$assets[$stack][$tag]['content'];
    }

    /**
     * Returns predefined stacks
     *
     * @return array
     */
    public static function getStacks()
    {
        return self::$stacks;
    }

    /**
     * Add a body class
     * Defined classes:
     * sidebar-white : makes side bg white
     * header-transparent: makes the main menu transparent
     * main-nav-no-border-bottom: This class allows you to add 'main-nav-no-border-bottom' to the body tag to remove the border bottom on the main menu
     * has-sidebar: The doc has sidebar.
     * has-minor-nav: The doc has minor navigation
     * navbar-$scheme: The scheme of the main nav. Where $scheme ={primary,subtle,etc.}
     * 
     * @param string $class
     * @return void
     */
    public static function registerBodyClass($class)
    {
        self::$bodyClasses[] = $class;
    }

    /**
     * eRemov a body class
     *
     * @param string $class
     * @return void
     */
    public static function unregisterBodyClass($class)
    {
        $key = array_search($class, self::$bodyClasses);
        if ($key !== false) {
            unset(self::$bodyClasses[$key]);
        }
    }

    public static function getBodyClassesString()
    {
        //dd(self::$getBodyClasses);
        return implode(' ', self::$bodyClasses);
    }

    /**
     * Sets the container type.
     *
     * @param string $type {fluid|static}
     * @param boolean $isImportant Set to true if the $type to be set is important for the working of the page. This helps to prevent unimportant overiding of the setting
     * @return boolean False if the type cannot be set
     */
    public function setContainerType($type = 'fluid', $isImportant = false)
    {
        if (!$isImportant and self::$containerType[1]) { //You can only overide an important setting with an important setting.
            return false;
        }

        if (!strcmp($type, 'fluid')) {
            self::$containerType[0] = 'fluid';
        } elseif (!strcmp($type, 'static')) {
            self::$containerType[0] = 'static';
        } else {
            self::$containerType[0] = 'static';
        }
        return true;
    }
    /**
     * Gets the container type
     *
     * @return string The container type
     */
    public static function getContainerType()
    {
        return self::$containerType[0];
    }

    /**
     * Checks if bootstrap container type is fluid
     * 
     * @param mixed $ontrue Variable to return on true
     * @param mixed $onfalse Variable to return on false
     * @return boolean|$ontrue|$onfalse
     */
    public static function isContainerFluid($ontrue = null, $onfalse = null)
    {
        $q = !strcmp(self::getContainerType(), 'fluid');
        if ($q and $ontrue) {
            return $ontrue;
        }
        if (!$q and $onfalse) {
            return $onfalse;
        }
        return $q;
    }
    /**
     * Gets the suffice added to the end of container class which is typically '-fluid' or ''.
     *
     * @return string
     */
    public static function getContainerSuffix()
    {
        $suffix = '';
        $type = self::getContainerType();
        if (!strcmp($type, 'fluid')) {
            $suffix = '-fluid';
        }
        return $suffix;
    }


    /**
     * Lightens/darkens a given colour (hex format), returning the altered colour in hex format.7
     * Source: https://gist.github.com/stephenharris/5532899
     * @param str $hex Colour as hexadecimal (with or without hash);
     * @percent float $percent Decimal ( 0.2 = lighten by 20%(), -0.4 = darken by 40%() )
     * @return str Lightened/Darkend colour as hexadecimal (with hash);
     */
    public static function colorLuminance($hex, $percent)
    {

        // validate hex string

        $hex = preg_replace('/[^0-9a-f]/i', '', $hex);
        $new_hex = '#';

        if (strlen($hex) < 6) {
            $hex = $hex[0] + $hex[0] + $hex[1] + $hex[1] + $hex[2] + $hex[2];
        }

        // convert to decimal and change luminosity
        for ($i = 0; $i < 3; $i++) {
            $dec = hexdec(substr($hex, $i * 2, 2));
            $dec = min(max(0, $dec + $dec * $percent), 255);
            $new_hex .= str_pad(dechex($dec), 2, 0, STR_PAD_LEFT);
        }

        return $new_hex;
    }

    /**
     * Returns the brands colors
     *
     * @return array
     */
    public static function getBrands()
    {
        return config('laradmin.brands');
    }
    /**
     * The logo type
     *
     * @param string $type
     * @return void
     */
    public static function registerLogoType($type)
    {
        self::$logoType = $type;
    }
    /**
      * Has the logo type been set?
      *
      * @return boolean
      */
    public static function hasLogoType()
    {
        return self::$logoType ? true : false;
    }
    /**
      * Gets the logo type
      * @param $append string Optional string to be appended to the begining of the output
      * @return string
      */
    public static function getLogoType($append = '')
    {
        if (self::hasLogoType()) {
            return $append . self::$logoType;
        } else {
            return self::$logoType;
        }
    }



    /**
     * Register navbar class on the body thus <body class="navbar-{{$class}}"> and then registeres a suitable logo. The current implementation is that logo.ext is normal ($class= default,subtle) or logo-white.ext for {$class=primary,danger,info,success,warning}. 
     *  TODO: Implement such that the corresponding logo is 'logo-{{$class}}.ext'
     * @param string $class
     * @return void
     */
    public static function registerMainNavScheme($class)
    {
        self::$mainNavClass = $class;
        self::registerBodyClass('navbar-' . $class);

        switch (strtolower($class)) {
            case 'default':
            case 'subtle':
                break;
            default:
                self::registerLogoType('white');
        }
    }
    /**
      * Has the navbar class been set?
      *
      * @return boolean
      */
    public static function hasMainNavClass()
    {
        return self::$logoType ? true : false;
    }
    /**
      * Gets the navbar class
      *
      * @return string
      */
    public static function getMainNavClass()
    {
        if (self::hasMainNavClass()) {
            return self::$mainNavClass;
        } else {
            return '';
        }
    }

    /**
      * Setup hero page
      *
      * @param string $img_url
      * @param string $type The hero type {see self::$heroType for example values}
      * @return void
      */
    public static function registerHero($img_url = null, $type = null)
    {
        if (!$type) {
            $type = 'hero';
        }
        self::$heroType = $type;
        //self::registerLogoType('white');

        self::registerBodyClass('main-nav-no-border-bottom');
        if (str_contains($type, 'super')) {
            self::registerBodyClass('hero hero-super');
            self::registerBodyClass('header-transparent');
        } else {
            self::registerBodyClass('hero');
        }


        if ($img_url) {
            $css = '<style type="text/css">.section.hero{
                    background-image: url(' . $img_url . ');
                }  
                </style>';
            self::registerAsset('head-styles', 'hero_image', $css);
        }
    }

    /**
      * Gets the Hero type
      *
      * @return string
      */
    public static function getHeroType()
    {
        return self::$heroType;
    }

    /**
     * Check if page is defined as having a super hero
     *
     * @param mixed $ontrue Variable to return on true
     * @param mixed $onfalse Variable to return on false
     * @return boolean|$ontrue|$onfalse
     */
    public static function isSuperHero($ontrue = null, $onfalse = null)
    {

        $q = str_contains(self::$heroType, 'super');
        if ($q and $ontrue) {
            return $ontrue;
        }
        if (!$q and $onfalse) {
            return $onfalse;
        }
        return $q;
    }
}
