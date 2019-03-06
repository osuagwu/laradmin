<?php
namespace BethelChika\Laradmin\Plugin\Contracts;

use BethelChika\Laradmin\Plugin\PluginManager;
use Illuminate\Foundation\Application;


interface Plugable{
    /**
     * Installation script.
     * Call your migration classes' up method here. 
     * Also in this method you can copy your assets into the vendor/{$plugin} folder in the public dir or make artisan call to publish.
     * 
     *
     * @param PluginManager $pluginmanager
     * @param string $tag
     * @return boolean
     */
    public function install(PluginManager $pluginmanager,$tag);

    /**
     * Publish assets and views and config. You can use \Artisan::call(...) to publish things defined in the plugin service provider
     *
     * @param PluginManager $pluginmanager
     * @param [type] $tag
     * @return void
     */
    public function publish(PluginManager $pluginmanager,$tag);
    
    /**
     * Uninstallation script
     * You can call you migration down methods to uninstall your migrations here
     * 
     * FIXME: This might be hard to do but remember to delete any assets you created as well when uninstalling your application.
     * 
     * @param PluginManager $pluginmanager
     * @param string $tag
     * @return boolean
     */
    public function uninstall(PluginManager $pluginmanager,$tag);






    /**
     * Disable script
     *
     * @param PluginManager $pluginmanager
     * @param string $tag
     * @return boolean
     */
    public function disable(PluginManager $pluginmanager,$tag);
   
     /**
     * Enale script
     *
     * @param PluginManager $pluginmanager
     * @param string $tag
     * @return boolean
     */
    public function enable(PluginManager $pluginmanager,$tag);

    /**
     * Registers your plugin. This method is called by the register method of a 
     * service provider so do not do here what you would not do in a service
     * provider register method. You may only need to register your plugin
     * service provider here.
     *
     * @param Application $app
     * @return void
     */
    public function register(Application $app);
}