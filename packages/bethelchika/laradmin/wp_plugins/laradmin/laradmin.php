<?php
/*
Plugin Name: Laradmin plugin
Plugon URI:
Description: Base plugin for Laradmin
Author: Bethel Chika
Author URI:
Version:0.1
*/

// Security to prevent direct access of php files
if(!defined('ABSPATH')){
    exit;
}


// Add post type
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
add_action('init','create_laradmin_cpt');

// Add admin menus
add_action('admin_menu','laradmin_menu');
function laradmin_menu(){
    add_menu_page('Laradmin','Laradmin',4,'laradmin-index','laradmin_index');

    // Add the custom post type as submenu
    add_submenu_page('laradmin-index','Laradmin page parts','Page parts',4,'edit.php?post_type=laradmin_page_part');
}
function laradmin_index(){
    echo '<h3>Laradmin is ready to go!</h3>
    <p> You can now head over to the <a href="edit.php?post_type=laradmin_page_part"> Page parts </a> to create <i>sidebar</i>, <i>rightbar</i> and <i>footer</i> page parts which you can use to add extra contents</p>
    <p>Note that all pages i.e post of post_type=="page", are now redirected to Laravel</p>
    ';
}

// Adding excerpt for page
add_post_type_support( 'page','excerpt' );

// Now add hook to redirect all pages to laravel
function redirect_pages_to_laravel()
{
    if( is_page(  ) )
    {
        $slug=get_post()->post_name;
        wp_redirect( '/page/'.$slug );
        die;
    }
}
add_action( 'template_redirect', 'redirect_pages_to_laravel' );

