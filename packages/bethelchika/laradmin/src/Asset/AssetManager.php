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
     * The navbar class the defines the look and feel of the main navigation.
     * Possible values include bootstrap brand classes ={primary,danger,success,
     * warning,info}; although some of these may not be implemented.
     *
     * @var string 
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
     * sidebar-white : makes side bg white. No tag required.
     * header-transparent: makes the main menu transparent. No tag required.
     * main-nav-no-border-bottom: This class allows you to add 'main-nav-no-border-bottom' to the body tag to remove the border bottom on the main menu. No tag required.
     * has-sidebar: The doc has sidebar. No tag required.
     * has-minor-nav: The doc has minor navigation. No tag required.
     * navbar-$scheme: The scheme of the main nav. Where $scheme ={primary,subtle,etc.}. Since there is more than one possible values for this, you may (not required) add the scheme with a tag. The corresponding tag=>navbar_scheme
     * 
     * @param string $class
     * @param string $tag A unique tag for the class
     * @return void
     */
    public static function registerBodyClass($class,$tag=null)
    {
        if($tag){
            self::$bodyClasses[$tag] = $class;
        }else{
            self::unregisterBodyClass($class);//remove class if it exists
            self::$bodyClasses[] = $class;
        }
        
    }

    /**
     * Removs a body class
     *
     * @param string $class
     * @param string $tag The unique tag of the class to remove.
     * @return void
     */
    public static function unregisterBodyClass($class,$tag=null)
    {
        if($tag){
            $key=$tag;
        }else{
            $key = array_search($class, self::$bodyClasses);
        }
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
        self::registerBodyClass('navbar-' . $class,'navbar_scheme');

        switch (strtolower($class)) {
            case 'default':
            case 'subtle':
                self::registerLogoType('');
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
      * @param array $img_urls Array of image urls indexed with 'sm' and 'lg' to specify the images screen sizes 
      * @param string $type The hero type {see self::$heroType for example values}
      * @return void
      */
    public static function registerHero($img_urls = null, $type = null)
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


        // Now do the image styles
        // Wrap images in ono-overlapping media  queries to avoid multiple downloads. See https://timkadlec.com/2012/04/media-query-asset-downloading-results/
        if (count($img_urls)) {
            $css = '
            <style type="text/css">
                @media all and (min-width: 768px){
                    .section.hero{
                        background-image: url(' . $img_urls['lg'] . ');
                    }
                }';
            if(isset($img_urls['sm']) and $img_urls['sm']){ 
                $css.='
                    @media all and (max-width: 767px){
                        .section.hero{
                            background-image: url(' . $img_urls['sm'] . ');
                        }
                    }
                </style>';
            }else{
                $css.='</style>';
            }
            
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
    public static function isHeroSuper($ontrue = null, $onfalse = null)
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

    /**
     * Here for when isHeroSuper(...) is mistyped. TODO: Delete this method after removing those using it
     *
     * @see self::isHeroSuper()
     */
    public static function isSuperHero($ontrue = null, $onfalse = null)
    {

        return self::isHeroSuper($ontrue,$onfalse);
    }
}
