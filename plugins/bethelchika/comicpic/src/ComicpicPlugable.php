<?php 
namespace BethelChika\Comicpic;

use BethelChika\Laradmin\Plugin\Contracts\Plugable;
use Illuminate\Support\Facades\Cache;
use BethelChika\Laradmin\Plugin\PluginManager;
use BethelChika\Comicpic\Models\Comicpic;
use BethelChika\Comicpic\Feed\Feed;
use Illuminate\Foundation\Application;
use BethelChika\Comicpic\ComicpicServiceProvider;
//use Illuminate\Support\Facades\Schema;



class ComicpicPlugable implements Plugable
{

    // public $title;
    // public $logo;
    // public $summary;
    private $migrationFile='/database/migrations/2018_05_18_000139_create_comicpics_table.php';

    public function __construct()
    {

        // $this->title = 'Comic Pic';
        // $this->logo = '/vendor/comicpic/img/logo.png';
        // $this->summary = 'Comical pictures and more!';

    }

    // public function getDisplayName()
    // {
    //     return $this->title;
    // }


    // /**
    //  * Get Version of plugin
    //  *
    //  * @return string
    //  */
    // public function getVersion()
    // {
    //     return '0.1';

    // }

    // /**
    //  * Get Description test
    //  *
    //  * @return string
    //  */
    // public function getDescription()
    // {
    //     return 'None Description';
    // }






    // /**
    //  * The Icon of the pluagble
    //  *
    //  * @return string
    //  */
    // public function getLogo()
    // {
    //     return $this->logo;
    // }

    // /**
    //  * Get the short description of the plugable
    //  *
    //  * @return string
    //  */
    // public function getSummary()
    // {
    //     return $this->summary;
    // }


    /**
     * @inheritDoc
     */
    public function install(PluginManager $pluginmanager,$tag)
    {
        $migration_file=$pluginmanager->getPluginPath($tag).$this->migrationFile;
        include($migration_file);
        (new \CreateComicpicsTable)->up();
        
        Cache::forever('comicpic.appname',$this->title);

   

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
            '--provider' => ComicpicServiceProvider::class,
            '--force' => true
        ]);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function uninstall(PluginManager $pluginmanager,$tag)
    {
        
        // Delete all comicpics
        foreach(Comicpic::all() as $comicpic){
            $comicpic->delete();
            Feed::delete($comicpic);//NOTE. We should not need to do this here. The comicpic delete method should auto handle this.
        }

        $paths=[public_path().'/vendor/comicpic',//assets
            base_path().'/resources/views/vendor/comicpic',//views
            //public_path().'/resources/lang/vendor/comicpic',//lang
            //base_path().'/config/comicpic.php'//config
        ];
        
        // Delete files
        foreach($paths as $path){
            try{//dd(glob($path.'/*/*/*'));
                $di = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
                $ri = new \RecursiveIteratorIterator($di, \RecursiveIteratorIterator::CHILD_FIRST);
                foreach ( $ri as $file ) {
                    $file->isDir() ?  rmdir($file) : unlink($file);
                }
                rmdir($path);
            }catch(\Exception $e){
                
            }
        }


        // Delete cache stores
        Cache::forget('comicpic.appname');
        
        //uninstall migration
        $migration_file=$pluginmanager->getPluginPath($tag).$this->migrationFile;
        include($migration_file);
        (new \CreateComicpicsTable)->down();
        
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
        $app->register(ComicpicServiceProvider::class);
    }
}