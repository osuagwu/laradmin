<?php
namespace BethelChika\Laradmin\WP\Traits;
trait Formatting{
    public function getContentFilteredAttribute()
    {
        $content_=$this->theContent($this->post_content);//NOTE: This needs to be done first b4 the short code else <p> and <br> will be added to mess up the outputs of shortcodes
        return $this->stripShortcodes($content_);
    }

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
       
        if (file_exists($formatting_file) and !function_exists('wptexturize')){

           include dirname(__DIR__).'/wp_helpers.php';

            include $formatting_file;

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
            $content=wptexturize($content);
            //$content=convert_smilies($content);
            $content=convert_chars($content);
            $content=wpautop($content);
        }

       


        return $content;
        
    }
}