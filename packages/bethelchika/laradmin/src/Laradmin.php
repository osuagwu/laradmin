<?php
namespace BethelChika\Laradmin;

use Illuminate\Support\Collection;
use BethelChika\Laradmin\Feed\FeedManager;
use BethelChika\Laradmin\Media\MediaManager;
use BethelChika\Laradmin\Plugin\PluginManager;
use BethelChika\Laradmin\Asset\AssetManager;
use BethelChika\Laradmin\Content\ContentManager;
//use BethelChika\Laradmin\Menu\Navigation;
use BethelChika\Laradmin\Permission\Permission;
use BethelChika\Laradmin\Theme\Contracts\Theme;
class Laradmin
{
    public $pluginManager;
    public $mediaManager;
    public $navigation;
    public $feedManager;
    public $assetManager;
    public $contentManager;
    public $permission;

    public $theme;

    /**
     * This is set to true when we are in admin pages
     *
     * @var boolean
     */
    private $isCp=false;


    public function __construct(
        MediaManager $mediaManager,
        FeedManager $feedManager,
        AssetManager $assetManager,
        ContentManager $contentManager,
        Permission $permission,
        Theme $theme
    ) {
        $this->mediaManager = $mediaManager;
        $this->feedManager = $feedManager;
        $this->assetManager = $assetManager;
        $this->contentManager=$contentManager;
        $this->permission=$permission;

        $this->theme=$theme;

        // Detect when we are in admin and make note
        if(isset($_SERVER['REQUEST_URI']) and strpos($_SERVER['REQUEST_URI'],'/cp/')===0){ // NOTE: this prohibits the use of 'domain/cp' i.e with the trailing slash '/'
            $this->isCp=true;
        }

    }

    /**
     * Checks if we are in admin pages
     *
     * @return boolean
     */
    public function isCp(){
        return $this->isCp;
    }

    public function resetMenus()
    {
        // Clear all menu
        $this->navigation->clearAll();

        // Create a menu
        $menu = new \BethelChika\Laradmin\Menu\Menu('primary', 'primary');

        // Add menu item
        $item_about = new \BethelChika\Laradmin\Menu\MenuItem('About', 'about');
        $item_about->namedRoute = 'about';
        $menu->addChild($item_about);

        // Add menu item
        $item_contact = new \BethelChika\Laradmin\Menu\MenuItem('Contact', 'contact');
        $item_contact->namedRoute = 'contact-us-create';
        $menu->addChild($item_contact);

        

        // Add menu to navigation
        $this->navigation->addMenu($menu);

        //Save the navigation to disk
        $this->navigation->store();
    }

}
