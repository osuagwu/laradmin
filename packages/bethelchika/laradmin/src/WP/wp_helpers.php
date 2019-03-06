<?php
/**
 * Some of the functions in /wp-includes/formatting.php for example calls the apply_filters(...) etc, here we will create a dummy version of it to avoid error deu to missing function

 */ 

/**
 * Some wordpress functions need. Here we create a dummy version.
 * wptexturize(...) will fail without this 
 */
global $shortcode_tags;
$shortcode_tags=[];

/**
 * Dummy function
 *
 * @param string $tag
 * @param string $value
 * @return string
 */       
function apply_filters($tag,$value){
    return $value;
}

/**
 * Dummy function
 *
 * @param string $text
 * @param string $context
 * @return string
 */
function _x($text,$context){
    return $text;
}

// /**
//  * Dummy function
//  *
//  * @param string $name
//  * @return null
//  */
// function get_option($name){
//     return null;
// }