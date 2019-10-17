<?php
/*
Plugin Name: Laradmin plugin
Plugon URI:
Description: Base plugin for Laradmin
Author: Bethel Chika
Author URI: http://www.webferendum.com
Version:0.1
*/

// Security to prevent direct access of php files
if(!defined('ABSPATH')){
    exit;
}

include __DIR__.'/inc/tools.php';


register_deactivation_hook( __FILE__, 'laradmin_deactivation' );
register_activation_hook( __FILE__, 'laradmin_activation' );

// Activation of plugin
function laradmin_activation(){
    update_option('wp_blogpost_on_laravel',1);
    flush_rewrite_rules();
}

// Deactivation of plugin
function laradmin_deactivation(){
    flush_rewrite_rules();
}


// Add page part custom post type
add_action('init','create_laradmin_cpt');
function create_laradmin_cpt(){
    $labels=array(
        'name'=>__('Laradmin page parts'),
        'singular_name'=> __('Laradmin page part'),
    );   
    register_post_type('laradmin_page_part',array(
                                                    'labels'=>$labels,
                                                    'public'      => false,
                                                    'show_ui'=>true,
                                                    'show_in_menu'=>false,
                                                )
                        );
}



// Add homepage section custom post type
function create_laradmin_hs_cpt(){
    $labels=array(
        'name'=>__('Laradmin homepage sections'),
        'singular_name'=> __('Laradmin homepage section'),
    );   

    $supports=['title','editor','author','thumbnail','excerpt',
                'custom-fields','revisions','page-attributes','post-formats'];
    
    register_post_type('laradmin_home_sec',array(
                                                    'labels'=>$labels,
                                                    'public'      => false,
                                                    'show_ui'=>true,
                                                    'show_in_menu'=>false,
                                                    'supports'=>$supports,
                                                )
                        );
}
add_action('init','create_laradmin_hs_cpt');




// Add larus_post custom post type
function create_laradmin_larus_cpt(){
    $labels=array(
        'name'=>__('Laradmin Larus posts'),
        'singular_name'=> __('Laradmin Larus post'),
    );   

    $supports=['title','editor','author','thumbnail','excerpt',
                'custom-fields','revisions','page-attributes','post-formats','comments','post-formats'];
    
    register_post_type('laradmin_larus_post',array(
                                                    'labels'=>$labels,
                                                    'public'      => false,
                                                    'show_ui'=>true,
                                                    'show_in_menu'=>false,
                                                    'supports'=>$supports,
                                                    'taxonomies'=>['category','post_tag'],
                                                )
                        );
}
add_action('init','create_laradmin_larus_cpt');



// Add admin menus
add_action('admin_menu','laradmin_menu');
function laradmin_menu(){
    add_menu_page('Laradmin','Laradmin',4,'laradmin-index','laradmin_index');

    // Add settings submenu
    add_submenu_page('laradmin-index','Settings','Settings',4,'laradmin-settings','laradmin_settings_page');

    // Add the custom post types as submenu
    add_submenu_page('laradmin-index','Laradmin page parts','Page parts',4,'edit.php?post_type=laradmin_page_part');
    add_submenu_page('laradmin-index','Laradmin homepage sections','Homepage sections',4,'edit.php?post_type=laradmin_home_sec');
    add_submenu_page('laradmin-index','Laradmin Larus posts','Larus posts',4,'edit.php?post_type=laradmin_larus_post');
}
function laradmin_index(){

    // Global app Routing
    if(isset($_REQUEST['action'])){
        switch($_REQUEST['action']){
            case 'create_factory':
                include __DIR__.'/factory/factory.php';
                $menu_name='primary';
                $r=create_laradmin_factory($menu_name);
                if($r==-1){
                    echo '<div class="notice notice-warning is-dismissible">Your WP installation may not be fresh enough for creating Laradmin factory. This may be because you have created nav menu with the same name as that the factory script was trying to create.</div>';
                }
                elseif($r>=1){
                    echo '<div class="notice notice-success is-dismissible">Laradmin factory was created!</div>';
                }
                else{
                    echo '<div class="notice notice-error is-dismissible">Unable to complete the creation of Laradmin factory.</div>';
                }
                break;
            default:
                echo '<div class="notice notice-warning is-dismissible">Unknown action, says Laradmin plugin routing</div>';
        }
    }

    // General Output
    echo '<h3>Laradmin is ready to go!</h3>
    <p> You can now head over to the <a href="edit.php?post_type=laradmin_page_part"> Page parts </a> to create <i>sidebar</i>, <i>rightbar</i> and <i>footer</i> page parts which you can use to add extra contents</p>

    <p>Also go to <a href="edit.php?post_type=laradmin_home_sec"> homepage sections </a> to create sections for home page</p>
    <p>Note that all pages i.e post of post_type=="page", are now redirected to Laravel</p>

    <h3>Factory data</h3>
    <p class="notice notice-info "> You can <a href="' .admin_url('admin.php').'?page=laradmin-index&action=create_factory"> install Laradmin factory data</a> which should let you test your installation.</p>
    ';
}

/**
 * Settings page
 *
 * @return string
 */
function laradmin_settings_page(){
    
    

    // Settings Routing

    // Set whether blog post should be handled by laravel
    if(isset($_POST['wp_blogpost_on_laravel'])){
        
        update_option('wp_blogpost_on_laravel',intval($_POST['wp_blogpost_on_laravel']));
    }

    // Now Show Settings

    echo '<h3>Laradmin settings</h3>';
    include __DIR__.'/tpl/settings.php';
    
}

// Adding excerpt for page
add_post_type_support( 'page', 'excerpt' );


// Now add hook to redirect to laravel
function redirect_to_laravel()
{
    //if( is_page(  ) )
    switch(get_post_type())
    {
        case 'page':
            $slug=get_post()->post_name;
            wp_redirect('/_page-wp-to-laradmin/'.$slug );//REF:PAGE-ROUTE-URL-PRE-WP-PLUG-1
            die;
        case 'laradmin_larus_post':
            $slug=get_post()->post_name;
            wp_redirect('/_larus-post-wp-to-laradmin/'.$slug );//REF:LARUS-POST-ROUTE-URL-PRE-WP-PLUG-1
            die;
        case 'post':
            if(get_option('wp_blogpost_on_laravel')){
                $slug=get_post()->post_name;
                wp_redirect('/_post-wp-to-laradmin/'.$slug );//REF:POST-ROUTE-URL-PRE-WP-PLUG-1
            }  
            die;
    }
}
add_action( 'template_redirect', 'redirect_to_laravel' );


// Define image sizes
add_action( 'after_setup_theme', 'laradmin_image_sizes' );
function laradmin_image_sizes() {
    // Define a medium image that makes sure that we have fixed size for 
    // all images with large enough dimension
    add_image_size( 'laradmin-thumb', 270, 150, true ); // (cropped)

    // Define a hero image for small screens
    add_image_size( 'laradmin-hero-sm', 800, 9999, true ); // (auto hieght)

    // Define a hero maximum size that prevents extremely large image size as hero, 
    // as it could load too slowly.
    add_image_size( 'laradmin-hero-lg', 1900, 9999, true ); // (auto hieght)
}



/**   
 * Now we will decide what what returned when inserting image to the editor. 
 */
add_filter( 'image_send_to_editor', 'add_custom_send_to_editor', 10, 8 );
function add_custom_send_to_editor( $html, $id, $caption, $title, $align, $url, $size, $alt ){  
    
    if( $id > 0 ){
        // $post = get_post( $id );
        // $media_data = array(
        //     $post->ID, // media id[0]
        //     $post->post_content, // media description
        //     $post->post_excerpt // media caption
        // );          
        // $img_size = wp_get_attachment_image_src($id, 'full'); // get media full size url
        // $data  = sprintf( ' data-media-description="%s"', esc_attr( $media_data[1] ) ); // set data-media-description
        // $data .= sprintf( ' data-media-url="%s" ', esc_url( $img_size[0] ) ); // set data-media-url
        // $html = str_replace( "<img src", "<img{$data}src", $html ); // replace and add custom attributes
        
        return  '[image_responsive id='.$id.' ]'.$html.'[/image_responsive]';
    }
   
    return $html;
}









