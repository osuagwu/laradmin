<?php
namespace BethelChika\Laradmin\Tools;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Corcel\Model\Option;

class Tools
{
    /**
     * Return the base path of laradmin
     *
     * @return string
     */
    public static function basePath()
    {
        $reflector = new \ReflectionClass(\BethelChika\Laradmin\LaradminServiceProvider::class);
        return dirname(dirname($reflector->getFileName()));
    }

    /**
     * Copy the Wordpress plugin
     *
     * @param boolean $force
     * @return integer The return value is -1=> if plugin is already installed and not reinstalled; 1=> for successfully installed and 0=>error.
     */
    public static function installWpPlugin($force = false)
    {
        if(!config('laradmin.wp_enable')){
            Log::error(__CLASS__.':'.__METHOD__.': msg=>WordPress is not enabled');
            return null;
        }
        $wp_plugins_rpath = '/wp-content/plugins'; //TODO: (perhaps set in config in future) CAUTION: We are assuming that wp plugin relative path is not changed from its default value.
        //$wp_tpls_rpath='/wp-contents/themes/'.trim(config('laradmin.wp_theme'),'\/').'/page_templates';//TODO: (perhaps set in config in future) CAUTION: We are assuming that wp theme relative path is not changed from its default value.


        $wp_plugins_path = (public_path() . '/' . trim(config('laradmin.wp_rpath'), '\/') . $wp_plugins_rpath);
        //$wp_tpls_path=(public_path().'/'.trim(config('laradmin.wp_rpath'),'\/').$wp_tpls_rpath);


        if (!$force and file_exists($wp_plugins_path . '/laradmin')) {
            return -1;
        } 
        else {
            try{
                
                self::rcopy(self::basePath() . '/wp_plugins/laradmin',  $wp_plugins_path.'/laradmin');
            }catch (\Exception $ex) {
                Log::error(__CLASS__.':'.__METHOD__.': msg=>'.$ex->getMessage());
                return 0;
            }

            return 1;
        }
    }

    /**
     * Create Wordpress templates
     *
     * @param boolean $force
     * @return integer The return value is -1=> if plugin is already installed and not reinstalled; 1=> for successfully installed and 0=>error.
     */
    public static function installWpTemplates($force = false)
    {
        if(!config('laradmin.wp_enable')){
            Log::error(__CLASS__.':'.__METHOD__.': msg=>WordPress is not enabled');
            return null;
        }
        $wp_theme=Option::get('template');
        if(!$wp_theme){
            Log::error(__CLASS__.':'.__METHOD__.': msg=>Could not optaine WordPress theme');
            return 0;
        }

        $wp_tpls_rpath = '/wp-content/themes/' . $wp_theme; //TODO: (perhaps set in config in future) CAUTION: We are assuming that wp theme relative path is not changed from its default value.


        $wp_tpls_path = (public_path() . '/' . trim(config('laradmin.wp_rpath'), '\/') . $wp_tpls_rpath);


        if (!$force and file_exists($wp_tpls_path . '/page_templates')) {
            return -1;
        } 
        else {

            // If the template folder is not created, make one.
            if (!file_exists($wp_tpls_path . '/page_templates')){
                mkdir($wp_tpls_path . '/page_templates');
            }

            $tpl_path = self::basePath() . '/resources/views/user/wp/page_templates';
            try {
                foreach (scandir($tpl_path) as $tpl) {
                    
                    if(str_is(['.','..'],$tpl)){
                        continue;
                    }
                    $name = str_ireplace('.blade.php', '', $tpl);
                    $filename = $wp_tpls_path . '/page_templates/' . $tpl;
                    $content = '<?php
                    /**
                     * Template Name: '.ucfirst(str_replace('_',' ',$name)).'
                     *
                     * @package Laradmin
                     * @subpackage WP
                     * @since  1.0
                     */';
                     //dd($filename);
                    file_put_contents($filename, $content);
                    
                }
            } catch (\Exception $ex) {
                Log::error(__CLASS__.':'.__METHOD__.': msg=>'.$ex->getMessage());
                return 0;
            }

            return 1;
        }
    }

    /**
     * Copy a directory to a destination recursively
     *
     * @param string $src
     * @param string $dst
     * @return void
     */
    public static function rcopy($src,$dst) { //dd($src);
        $dir = opendir($src); 
        @mkdir($dst); 
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 
                    self::rcopy($src . '/' . $file,$dst . '/' . $file); 
                } 
                else { 
                    copy($src . '/' . $file,$dst . '/' . $file); 
                } 
            } 
        } 
        closedir($dir); 
    } 

    

   


}
