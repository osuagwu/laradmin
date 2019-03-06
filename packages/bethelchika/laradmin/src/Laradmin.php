<?php
namespace BethelChika\Laradmin;

use Illuminate\Support\Collection;
use BethelChika\Laradmin\Feed\FeedManager;
use BethelChika\Laradmin\Media\MediaManager;
use BethelChika\Laradmin\Plugin\PluginManager;
use BethelChika\Laradmin\Asset\AssetManager;
use BethelChika\Laradmin\Content\ContentManager;
//use BethelChika\Laradmin\Menu\Navigation;

class Laradmin
{
    public $pluginManager;
    public $mediaManager;
    public $navigation;
    public $feedManager;
    public $assetManager;
    public $contentManager;

    public function __construct(
        MediaManager $mediaManager,
        FeedManager $feedManager,
        AssetManager $assetManager,
        ContentManager $contentManager
    ) {
        $this->mediaManager = $mediaManager;
        $this->feedManager = $feedManager;
        $this->assetManager = $assetManager;
        $this->contentManager=$contentManager;


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
