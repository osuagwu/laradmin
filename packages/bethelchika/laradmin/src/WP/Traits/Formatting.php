<?php
namespace BethelChika\Laradmin\WP\Traits;
trait Formatting{
    public function getContentFilteredAttribute()
    {
        return $this->theContent($this->content);
    }

    public function getExcerptFilteredAttribute(){
        if(strlen($this->excerpt)){
            return $this->theContent($this->excerpt);
        }
        else {
            # code...
            $c= $this->theContent($this->content);
            if(strlen($c)>240)
                $c=substr($this->content,0,240).' ...';
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
       
        if (file_exists($formatting_file)){

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