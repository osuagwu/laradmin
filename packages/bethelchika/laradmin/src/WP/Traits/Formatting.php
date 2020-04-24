<?php
namespace BethelChika\Laradmin\WP\Traits;

use Illuminate\Support\Facades\Log;

trait Formatting{

    /**
     * Keeps the processed content of the post
     *
     * @var string
     */
    private $processedContents=['mainbar'=>null,
                                'sidebar'=>null,
                                'rightbar'=>null,
                                'footer'=>null,    
    ];

    /**
     * Tells when the contents has been processed
     *
     * @var boolean
     */
    private $hasProcessedContents=false;

    /**
     * Filtered all contents and keep them for easy access later
     *
     * @return void
     */
    public function filterContents(){
        $this->processedContents['mainbar']=$this->theContent($this->post_content);
        $this->processedContents['sidebar']=$this->theContent($this->getSidebar(false));
        $this->processedContents['rightbar']=$this->theContent($this->getRightbar(false));
        $this->processedContents['footer']=$this->theContent($this->getFooter(false));

        $this->hasProcessedContents=true;
    }

    /**
     * Get the filtered content
     * 
     * @return string
     */
    public function getContentFilteredAttribute()
    {
        if(!$this->hasProcessedContents){
            $this->filterContents();
        }
        return $this->processedContents['mainbar'];
        //return $this->theContent($this->post_content);
        
    }

     /**
     * Get the filtered content for sidebar
     * 
     * @return string
     */
    public function getSidebarFilteredAttribute()
    {
        if(!$this->hasProcessedContents){
            $this->filterContents();
        }
        return $this->processedContents['sidebar'];        
    }

         /**
     * Get the filtered content for rightbar
     * 
     * @return string
     */
    public function getRightbarFilteredAttribute()
    {
        if(!$this->hasProcessedContents){
            $this->filterContents();
        }
        return $this->processedContents['rightbar'];        
    }

    /**
     * Get the filtered content for footer
     * 
     * @return string
     */
    public function getFooterFilteredAttribute()
    {
        if(!$this->hasProcessedContents){
            $this->filterContents();
        }
        return $this->processedContents['footer'];        
    }


    /**
     * Get the filtered excerpt
     *
     * @return string
     */
    public function getExcerptFilteredAttribute(){
        if(strlen($this->excerpt)){
            return $this->theContent($this->excerpt);
        }
        else {
            # code...
            $c= $this->theContent($this->post_content);
            if(strlen($c)>240)
                $c=substr($this->post_content,0,240).' ...';
            return $c;
        }
    }

    /** 
     * Wordpress' the_content simulation
     * 
     * @param string $content
     * @return string The content
     */
    private function theContent($content){

        $formatting_file=public_path().config('laradmin.wp_rpath').'/wp-includes/formatting.php';//NOTE: were assuming that the includes path for wordpress is not chnaged
        $XSS_file=public_path().config('laradmin.wp_rpath').'/wp-includes/kses.php';// html excape against XSS:: Note that this is really not require as people who will write posts already have access files to 
       
        if (file_exists($formatting_file) ){
            if(!function_exists('wptexturize')){// just a way to avoid loading the functions multiple times
                include dirname(__DIR__).'/wp_helpers.php';
                include $formatting_file;
                include $XSS_file;
            }
            

            /*the_content {Processing pipeline}
            8   (object) WP_Embed -> run_shortcode (1) 
                (object) WP_Embed -> autoembed (1) 
            10  wptexturize (1) 
                convert_smilies (1) 
                convert_chars (1) 
                wpautop (1) 
                shortcode_unautop (1) 
                prepend_attachment (1) 
            11  capital_P_dangit (1) 
                do_shortcode (1) 
            */ 
            // Perform the items in the pipeline as much as we can
            $content=wpautop($content);//This could mess up the output of shortcodes with extra <p> and <br> so we run it before shortcodes.

            $content= $this->stripShortcodes($content); //Now manually run the short codes instead of using accessing the 'content' attribute of the post to auto run it.
            
            $content=convert_chars($content);
            $content=wptexturize($content);// This messes up the shortcodes so must be run after shortcode is applied
            //$content=convert_smilies($content);

            $content=wp_kses_post($content);
        }else{
            Log::warning(__METHOD__.','.__LINE__.': msg=> Wordpress function for formatting post contents; you should make sure that contents are well processed and stripped of dangerous tags.');
        }

        return $content;
        
    }
}