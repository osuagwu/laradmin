<?php 
namespace BethelChika\Laradmin\Plugin;

use Illuminate\View\View;
use App\Plugin\Contract\Plugable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
//use function GuzzleHttp\json_decode;


class PluginManager{
 

    public function __construct(){
        //$this->plugableNames=new Collection;
    }
        
    /**
     * Returns the filename containing update script name
     * @param string $tag The Tag of a plugin. 
     * @return string 
     */
    private function getUpdateFilename($tag){
        return $this->getPluginPath($tag).'/update/update.php';
    }

     /**
     * Returns the filename containing update cancel script name
     * @param string $tag The Tag of a plugin. 
     * @return string 
     */
    private function getUpdateCancelFilename($tag){
        return $this->getPluginPath($tag).'/update/cancel.php';
    }

    // public function registerPlugin($plugableName){
    //     $this->plugableNames->push($plugableName);
    // }

    

    // /**
    //  * Get info of a given plugin
    //  *
    //  * @return Array
    //  */
    // public function getInfo($plugableName){
        
    //     $plugable= new $plugableName;
    //     $info=['display_name'=>$plugable->getDisplayName(),
    //         'logo'=>$plugable->getLogo(),
    //         'summary'=>$plugable->getSummary(),
    //         'description'=>$plugable->getDescription(),
    //     ];
    //     return $info;
    // }

    /**
     * Returns array of all plugins
     *
     * @return array All plugins
     */
    public function all(){
        $pluginspath=$this->getPluginsPath();
        $vendors = new \DirectoryIterator($pluginspath);
        $details=[];
        foreach ($vendors as $vendor) { //GO through all the vendors
            if ($vendor->isDot()) {
                continue;
            }
            if($vendor->isDir()){
                $plugins = new \DirectoryIterator($vendor->getRealPath());
                foreach ($plugins as $plugin) {//GO through all folders in the vendor directory which each should be a plugin
                    if ($plugin->isDot()) {
                        continue;
                    }
                    $d=[];
                    
                    if($plugin->isDir()){//i.e pluging must be a folder
                        $tag=$vendor.'/'.$plugin->getFilename();
                        $d=$this->getDetails($tag,false);
                           
                    }
                    
                    $details[]=$d;
                    
                    
                }

                
                //echo $fileinfo->getFilename() . "\n";
            }
        
        }
        return $details;
    
    }




   




    /**
     * Return details of a given plugin
     *
     * @param string $tag The Tag of a plugin. 
     * @param boolean $strict_check Checks the validity of the plugin when true. Some of the checks can lead to fatal error when the plugin fails the test
     * @return array Details of plugin
     */
    public function getDetails($tag,$strict_check=false){
        $d['error_count']=0;
        $d['error_msgs']=[];
        $d['tag']=$tag;
        $d['title']=str_replace('/',':',$tag);
        $d['description']=null;
        $d['plugable']=null;
        $d['psr4']=null;
        $d['installed']=null;
        $d['updating']=null;
        $d['img_url']=null;
        $d['thumbnail_url']=null;
        

        
        $pluginpath=$this->getPluginPath($tag);
        if(!file_exists($pluginpath)){
            $d['error_count']=1;
            $d['error_msgs'][]='Missing plugin folder';
            return $d;
        }

         //Get the image
         if(file_exists($pluginpath.'/img.jpg')){
            $d['img_url']=route('cp-plugin',['tag'=>$tag,'show_img'=>'1']);
            $d['thumbnail_url']=route('cp-plugin',['tag'=>$tag,'show_img'=>1,'thumbnail'=>1]);
        }


        $plugin = new \DirectoryIterator($pluginpath);

        if(file_exists($plugin->getRealPath().'/composer.json')){
            $c=file_get_contents($plugin->getRealPath().'/composer.json');//must have composer .json
            $js=json_decode($c);
            if(isset($js->description)){
                $d['description']=$js->description;
            }
            
            // Load psr4
            //dd($js);
            if(isset($js->autoload)){
                if(isset($js->autoload->{'psr-4'})){
                    $d['psr4']=$js->autoload->{'psr-4'};
                    //var_dump($js->autoload->{'psr-4'});
                    
                }
            }
            
            if(isset($js->extra->plugable)){
                $d['plugable']=$js->extra->plugable;
                
                if($strict_check){
                    //TODO: Use reflection to check if plagable is abstarct in which cse it has not implemented all methods
                    //first check if plugable is fine 
                    $plug=null;  
                    try{ //Possible fatal errors here which cannot be caught by this try statement include 
                        //"class not found" and "intantiation of abstract class". This is fine as it prevents 
                        //the plugin from beign install which will crash the entire website

                        if(!class_exists($js->extra->plugable)){
                            //print($js->extra->plugable);
                            $this->loadPsr4($d['psr4'],$pluginpath);
                        }
                        $plug=new $js->extra->plugable;//NOTE that a plugin can cause the entire application to break here 
                                                        //if the plugable does not exist,  even after the psr4 defined above. 
                                                        //The try statement is here is useless and will not catch the type of 
                                                        //error. The application will also break if  plugable implementation is 
                                                        //incorrect This is fine as it prevents the plugin from beign install which 
                                                        //will crash the entire website.
                    }catch(\Exception $e){
                        $d['error_msgs'][]=$e->getMessage();
                        $d['error_count']=$d['error_count']+1;
                    }

                    //Then check if it inplements plugable contract
                    if($plug) {
                        $interfaces=class_implements($plug);
                        if (isset($interfaces['BethelChika\Laradmin\Plugin\Contracts\Plugable'])){
                            //Great
                        }else{
                            var_dump($plug);
                            $d['error_msgs'][]= 'Plugable must implement the Plugable contract ';
                            $d['error_count']=$d['error_count']+1;
                            
                        }
                    }else{
                        $d['error_msgs'][]='Unknown issue with Plugable';
                        $d['error_count']=$d['error_count']+1;
                    }
                }
            }else{
                $d['error_msgs'][]= 'Missing plugable';
                $d['error_count']=$d['error_count']+1;
            }
            if(isset($js->extra->title)){
                $d['title']=$js->extra->{'title'};
            }
        }else{
            $d['error_count']=$d['error_count']+1;
            $d['error_msgs'][]='Missing composer.json';
        }
        $plugin=
        $d['installed']= $this->isInstalled($d['tag']);
        $d['updating']=$this->isUpdating($d['tag']);  


       

        return $d;
    }

    /**
     * Returns the path where all plugins are
     *
     * @return string Plugins path
     */
    public function getPluginsPath(){
        /**
         * If $pluginPath has has empty string we set it to 'laravel root /plugins'
         * __DIR__ is either '../packages/bethelchika/laradmin/src/plugin' or '../vendors/bethelchika/laradmin/src/plugin'
         * So to get to '../plugins' we call dirname 5 times and add '/plugins' to the result.
         */
        $pluginspath=config('laradmin.plugins_path','');
        if(!$pluginspath){
            $path=__DIR__;
            $pluginspath=dirname(dirname(dirname(dirname(dirname(($path)))))).'/plugins';
            //dd( $pluginsPath);
        }
        return $pluginspath;
    }

     /**
     * Returns the base path of the given plugin
     *
     * @param string $tag Plugin tag
     * @return string Pling path
     */
    public function getPluginPath($tag){
        
        return str_replace('//','/',$this->getPluginsPath().'/'.$tag);
    }

    /**
     * Installs the plugin
     * TODO: refuse to install if details has errors.
     * @param string $tag Plugin tag
     * @return int Return 1 when sucessfull but -1 if already installed and 0 for error
     */
   public function install($tag){
       $d=$this->getDetails($tag,true);//True for second argument forces checks which forces bad plugins to fail the installation. But it also makes sure that psr-4 is loaded
       
       if($this->isInstalled($tag)){
           return -1;
       }
       

       try{
            $plug=new $d['plugable'];
            $re=$plug->install($this,$tag);
            if(!$re){
                return 0;//Something went wrong with installation
            }
       }catch(\Exception $e){           
           Log::error('Error installing plugin:(tag='.$tag.'):> '.$e->getMessage());
            return 0;
       }

       

        $c=new \stdClass();
        $c->tag=$tag;//Plugin unique identifier
        $c->enabled=1;//When true, pluging is enabled
        $c->plugable=$d['plugable'];//plugable fully qualified classname
        $c->psr4=$d['psr4'];//prs4 definitions
        $c->updating=0;// When true update is in progress
        
        $plugins=$this->installation();//get installed plugins
        $plugins->installed[]=$c;//add the new installation
        $plugins->installed=array_values($plugins->installed);//There is not reason the array order should change here but just go ahead a reindex with array values anyway

        $this->installation($plugins);
        return 1;

   }

    /**
     * Installs the plugin
     * TODO: refuse to publish if details has errors.
     * @param string $tag Plugin tag
     * @return int Return 1 when sucessfull but -1 if not installed and 0 for error
     */
    public function publish($tag){
        //Since the plugin is already installed, its psr4 should already have been loaded
        $d=$this->getDetails($tag);
        
        if(!$this->isInstalled($tag)){
            return -1;
        }
        
 
        try{
             $plug=new $d['plugable'];
             $re=$plug->publish($this,$tag);
             if(!$re){
                 return 0;//Something went wront with publishing
             }
        }catch(\Exception $e){           
            Log::error('Error publishing plugin:(tag='.$tag.'):> '.$e->getMessage());
             return 0;
        }
 
         
         return 1;
 
    }
 

      /**
     * Uninstalls the plugin
     *
     * @param string $tag Plugin tag
     * @return int Return 1 when sucessfull but -1 if not installed and 0 if something went wrong
     */
    public function uninstall($tag){
        //Fisrt check if it is installed
        $state=$this->isInstalled($tag);
        if(!$state){
            return -1;
        }

        // Get details and load psr4 if needed
        $d=$this->getDetails($tag);
        if($state==-1){
            $this->loadPsr4($d['psr4'],$this->getPluginpath($tag));
        }
        
        //Try to do the uninstallation
        try{
            $plug=new $d['plugable'];
            if(!$plug->uninstall($this,$tag)){
                return 0;
            }
       }catch(\Exception $e){
           //TODO: return some message to help with diagnosis although the errors might be uncatchable;
           
           Log::error('Error uninstalling plugin:(tag='.$tag.'):> '.$e->getMessage());
           
            return 0;
       }

        $plugins=$this->installation();
        for( $i=0;$i<count($plugins->installed); $i++){
            if(!strcmp($tag,$plugins->installed[$i]->tag)){
                unset($plugins->installed[$i]);
            }
        }
         $plugins->installed=array_values($plugins->installed);//reindex so that it is in order so that it is not seem to be associative when encoding to json
         $this->installation($plugins);
         return 1;
 
    }



    /**
     * Initiate plugin update
     * @param string $tag Plugin tag
     * @return int Returns 1 if successful or -1 if already update in progress, -2 if  the update scripts are missing, -3 if update-cancel script is missing, 0 if error or not installed or disabled etc.
     */
    public function updating($tag){
        if(!file_exists($this->getUpdateFilename($tag))) {
            return -2;
        }
     
        if(!file_exists($this->getUpdateCancelFilename($tag))){
            return -3;
        }
        
        $plugin_state=$this->isInstalled($tag);
        if($plugin_state!=1){
            return 0;
        }
        $result=$this->setInstallation($tag,'updating',1);
        //dd($result);
        return $result;
 
    }

    /**
     * Cancel plugin update
     * @param string $tag Plugin tag
     * @return int Returns 1 if successful or -1 if alreday update in progress, 0 if error or not installed or disabled etc.
     */
    public function updateCancel($tag){
        $state=$this->isInstalled($tag);
        if($state!=1){
            return 0;
        }

        // Get details and load psr4 if needed
        $d=$this->getDetails($tag);
           
        //Try to do the cancel
        try{
            $cancel=include $this->getUpdateCancelFilename($tag);
            
            if(!$cancel){
                return 0;
            }
       }catch(\Exception $e){
           //TODO: return some message to help with diagnosis although the errors might be uncatchable;
           
           Log::error('Error canceling plugin update:(tag='.$tag.'):> '.$e->getMessage());
           
            return 0;
       }

        $result=$this->setInstallation($tag,'updating',0);
        return $result;
 
    }



    /**
     * Update pluign spefified by tag. 
     * @param string $tag Plugin tag
     * @return int Returns 1 success, 0 if no update in progress -1 and 0 otherwise
     */
    public function update($tag){
        $plugin=$this->getInstallation($tag);
        if(!$plugin->updating){
            return -1;
        }

        // Get details 
        $d=$this->getDetails($tag);
           
        //Try to do the update
        try{
            $update=@include $this->getUpdateFilename($tag);
            if(!$update){
                return 0;
            }
       }catch(\Exception $e){
           //TODO: return some message to help with diagnosis although the errors might be uncatchable;
           Log::error('Error updating plugin :(tag='.$tag.'):> '.$e->getMessage());
            return 0;
       }


        $result=$this->setInstallation($tag,'updating',0);
        return $result;
    }

    /**
     * Checks if plugin is intalled/enabled
     *
     * @param string $tag Plugin tag
     * @return int Returns 1 if is installed and enabled, 0 if is not installed and -1 if disabled
     */
    public function isInstalled($tag){
        $plugins=$this->installation();
 
         foreach($plugins->installed as $plugin){
             if(!strcmp($tag,$plugin->tag)){
                if($plugin->enabled)
                    return 1;//installd and enabled
                return -1; //installed disabldd
             }
         }
         return 0; //not intalled
    }

    /**
     * Checks if plugin is updating
     *
     * @param string $tag Plugin tag
     * @return int Returns 1 if is updating, 0 if not
     */
    public function isUpdating($tag){
        $plugins=$this->installation();
 
         foreach($plugins->installed as $plugin){
             if(!strcmp($tag,$plugin->tag)){
                if($plugin->updating)
                    return 1;
             }
         }
         return 0; //not intalled
    }

  /**
     * Enable plugin
     * TODO: refuse to enable if details has errors.
     * @param string $tag Plugin tag
     * @return int Returns 1 if successful or -1 if alreday enabled, 0 otherwise.
     */
    public function enable($tag){
        
        if(!$this->isInstalled($tag)){
            return 0;
        }
        $d=$this->getDetails($tag);
        $this->loadPsr4($d['psr4'],$this->getPluginpath($tag));

        try{
            $plug=new $d['plugable'];
            if(!$plug->enable($this,$tag)){
                return 0;
            }
       }catch(\Exception $e){
           //TODO: return some message to help with diagnosis although the errors here might be uncatchable;
           
           Log::error('Error enabling plugin:(tag='.$tag.'):> '.$e->getMessage());
           
            return 0;
       }

        $plugins=$this->installation();
 
         foreach($plugins->installed as $plugin){
             if(!strcmp($tag,$plugin->tag)){
                if($plugin->enabled==1){
                    return -1;//Alreday enabled
                }
                 $plugin->enabled=1;
                 break;
             }
         }
 
         $this->installation($plugins);
         return 1;
 
    }

      /**
     * Disable plugin
     *
     * @param string $tag Plugin tag
     * @return int Returns 1 if successful or -1 if alreday disabled, 0 otherwise.
     */
    public function disable($tag){
        
        if(!$this->isInstalled($tag)){
            return 0;
        }

        $d=$this->getDetails($tag);
        try{
            $plug=new $d['plugable'];
            if(!$plug->disable($this,$tag)){
                return 0;
            }
       }catch(\Exception $e){
           //TODO: return some message to help with diagnosis although the errors might be uncatchable;
           
           Log::error('Error disabling plugin:(tag='.$tag.'):> '.$e->getMessage());
           
            return 0;
       }

        $plugins=$this->installation();
 
         foreach($plugins->installed as $plugin){
             if(!strcmp($tag,$plugin->tag)){
                if($plugin->enabled==0){
                    return -1;//Alreday enabled
                }
                 $plugin->enabled=0;
                 break;
             }
         }
 
         $this->installation($plugins);
         return 1;
 
    }

    /**
     * Sets intallation property of a tag speficifed plguin 
     *
     * @param string $tag Plugin tag
     * @param mixed $prop The property/key to set
     * @param mixed $val The value to be assigned to $prop
     * @return mixed Returns 1, -1 if values has been set already, 0 not installed or property does not exist or otherwise any error.
     */
    private function setInstallation($tag,$prop,$val){
        
        if(!$this->isInstalled($tag)){
            return 0;
        }
        $plugins=$this->installation();
 
         foreach($plugins->installed as $plugin){
             if(!strcmp($tag,$plugin->tag)){
                if(!property_exists($plugin,$prop)){
                    return 0;//Alreday enabled
                }
                if(!strcmp($plugin->$prop,$val)){
                    return -1;
                }
                $plugin->$prop=$val;
                break;
             }
         }
 
         $this->installation($plugins);
         return 1;
 
    }

/**
     * Return intallation object for a tag speficifed plguin 
     *
     * @param string $tag Plugin tag
     * @return obj plugin Object or false
     */
    private function getInstallation($tag){
        $plugins=$this->installation();
         foreach($plugins->installed as $plugin){
             if(!strcmp($tag,$plugin->tag)){
                return $plugin;
                break;
             }
         }
 
         return false;
 
    }
  


   /**
    * Returns installation file convertng it to object when no parameter given otherwise replaces the intallation file with parameter value converted to json
    *
    * @return stdClass|null
    */
   private function installation($js=null){
        $ins=$this->getPluginsPath().'/plugins.json';
       if($js){
            file_put_contents($ins,json_encode($js));
       }else{
           
           return json_decode(file_get_contents($ins));//TODO: instead of accessing the disk all the time, it might be better to do this onece and return the content from a quick access memory at subsequent calls
       }
       
   }

   /**
    * Load plugins psr-4
    *
    * @param stdClass $prs4
    * @param string $base_path
    * @return void
    */
   public function loadPsr4($psr4,$base_path){

        foreach($psr4 as $prefix=>$path){
            if(!is_array($path)){
                $path=[$path];
            }
            foreach($path as $p){
                
                $this->psr4($prefix,$base_path.'/'.$p);    
            }
            
        }
   }

   /**FIXME:; Should be moved to a more general location for the entire laradmin rather than for this class alone
    * Register autoloads for a giving detail 
    *
    * @param string $namespace_prefix The base Namespace
    * @param string $base_path Full path of the base directory.
    * @return void
    */
   public function psr4($namespace_prefix,$base_path){
        //dd($namespace_prefix.'...'.$base_path);
        //$plugin_base_dir=$this->getPluginsPath().'/'.$_path;
        spl_autoload_register(function ($class)use ($namespace_prefix,$base_path) {
            $prefix=ltrim($namespace_prefix,'\\');//"BethelChika\\Comicpic\\";
            $base_dir = $base_path;//__DIR__ . '/src/';
            $class = ltrim($class, '\\');
            
            // does the class use the namespace prefix?
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                // no, move to the next registered autoloader
                return;
            }
            
                // get the relative class name
            $relative_class = substr($class, $len);
            
            // replace the namespace prefix with the base directory, replace namespace
            // separators with directory separators in the relative class name, append
            // with .php
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
            
            // if the file exists, require it
            if (file_exists($file)) {
                require $file;
            }
            
        });

   }



    // /**
    //  * Get info of plugins
    //  *
    //  * @return Collection
    //  */
    // public function getAllInfo(){
    //     $infos=new Collection;
    //     foreach($this->plugableNames as $plugableName){
    //         $infos->push($this->getInfo($plugableName));
    //         //dd($infos);
    //     }
    //     return $infos;
        
    // }

    /**
     * Accepts the view name and the parameters to display it for a plugin admin settings pages
     *
     * @param string $viewname
     * @param array $data
     * @return Illuminate\Http\RedirectResponse 
     */
    public function adminView($viewname,$data=[]){
        //$content=$view->render();
        return \App::call('\\BethelChika\Laradmin\Http\Controllers\CP\PluginAdminController@pluginVendorAdminView',
                            ['viewname'=>$viewname,'data'=>$data]);
    }

    /**
     * Accepts the view name and the parameters to display it for a plugin users settings pages
     *
     * @param string $view
     * @param array $data
     * @return Illuminate\Http\RedirectResponse 
     */
    public function userView($viewname,$data=[]){
        return \App::call('\\BethelChika\Laradmin\Http\Controllers\User\PluginUserController@pluginVendorUserView',
                            ['viewname'=>$viewname,'data'=>$data]);
    }

    
}