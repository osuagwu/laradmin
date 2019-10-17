<?php 
namespace BethelChika\Ulooma;

use BethelChika\Laradmin\Plugin\Contracts\Plugable;
use BethelChika\Laradmin\Plugin\PluginManager;
use BethelChika\Laradmin\Theme\Contracts\Theme;
use Illuminate\Foundation\Application;
use BethelChika\Ulooma\UloomaServiceProvider;



/**
 * A theme Plugable. The Plugable also extends Theme contract but this is not necessary as the 
 * separate class can be cleanly created to extends the Theme contract, to let the plugable
 * be jus  Plugable.
 */
class UloomaPlugable extends Theme implements Plugable 
{

    

    public function __construct()
    {

        // Define theme details
        $this->name='Ulooma';
        $this->from='ulooma::';
    }

    

    /**
     * @inheritDoc
     */
    public function install(PluginManager $pluginmanager,$tag)
    {
        

   

        return true;
    }

     /**
     * @inheritDoc
     */
    public function publish(PluginManager $pluginmanager,$tag)
    {
        //Use artisan to publish
        \Artisan::call('vendor:publish',
        [
            '--provider' => UloomaServiceProvider::class,
            '--force' => true
        ]);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function uninstall(PluginManager $pluginmanager,$tag)
    {
        
        
        return true;
        
    }

 
//    /**
//      * @inheritDoc
//      */
//     public function update(PluginManager $pluginmanager, $tag)
//     {
//         return true;

//     }
     /**
     * @inheritDoc
     */
    public function disable(PluginManager $pluginmanager, $tag)
    {
        return true;
    }
     /**
     * @inheritDoc
     */
    public function enable(PluginManager $pluginmanager,$tag)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function register(Application $app){
        //Register service provider
        $app->register(UloomaServiceProvider::class);
    }
}