<?php

namespace BethelChika\Laradmin\Plugin;

use Illuminate\Support\ServiceProvider;
class PluginServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //Register Navigation singleton
        $this->app->singleton('BethelChika\Laradmin\Plugin\PluginManager', function ($app) {
            return new PluginManager();
        });
        //$this->app->alias('BethelChika\Laradmin\Plugin\PluginManager','pluginmanager');//OPEN THIS if you want to access the singleton directly instead of through laradmin

        // Attach pluginmanager to Laradmin so that we can access it through laradmin
        $pluginmanager=$this->app->make('laradmin')->pluginManager=$this->app->make('BethelChika\Laradmin\Plugin\PluginManager');
        
        // Load plugins       
        /**
         * If $pluginPath has empty string we set it to 'laravel root /plugins'
         * __DIR__ is either '../packages/bethelchika/laradmin/src/plugin' or '../vendors/bethelchika/laradmin/src/plugin'
         * So to get to '../plugins' we call dirname 5 times and add '/plugins' to the result.
         */
        $pluginsPath=config('laradmin.plugins_path','');
        if(!$pluginsPath){
            //$path=__DIR__;
            //$pluginsPath=dirname(dirname(dirname(dirname(dirname(($path)))))).'/plugins';
            //dd( $pluginsPath);
            $pluginsPath=base_path().'/plugins';
        }
        if(file_exists($pluginsPath.'/plugins.json')){
            $plugins=json_decode(file_get_contents($pluginsPath.'/plugins.json'));
            foreach($plugins->installed as $plugin){
                if($plugin->enabled and !$plugin->updating){
                    //$pl=$pluginsPath.'/'.$plugin->tag.'/plugin.php';
                    //require($pl); 
                    //dd($plugin);

                    $pluginmanager->loadPsr4($plugin->psr4,$pluginmanager->getPluginpath($plugin->tag));
                    
                    $plug=new $plugin->plugable;
                    $plug->register($this->app,$plugin->tag);
                }
            }
        }

        
        
    }

    /**
     * Bootstrap the application services.
     * 
     * @return void
     */
    public function boot()
    {
         //Create admin menu
         $this->app->make('laradmin')->navigation->create('Plugins','plugins','admin.general',[
            'namedRoute'=>'cp-plugins',
            'iconClass'=>'fas fa-plug',
            ]);
    
    }

    
}
