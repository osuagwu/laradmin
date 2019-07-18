<?php
/**
 * Security to prevent direct access of php files
 */
if(!defined('ABSPATH')){
    exit;
}

/**
 * Create laradmin factory
 * @param $menu_name A menu name where all nav items will be added.
 * @return int Postive integer on success, and 0=>on fail and -1=>seems like action already performed.
 */
function create_laradmin_factory($menu_name){
    $menu_exists = wp_get_nav_menu_object( $menu_name );

    // If it doesn't exist, let's create it.
    if($menu_exists){
        return -1; //Do not do anything as the install is not as fresh as we would like. 
    }
    else{        
        return create_laradmin_factory_items($menu_name);
    }
    
    
} 

/**
 * Create factory items for laradmin
 *
 * @param string $menu_name A menu where all all nave items will be added.
 * @return int Postive integer on success, and 0=>on fail.
 */
function create_laradmin_factory_items($menu_name){

    //Create the main nav menu
    $menu_id = wp_create_nav_menu($menu_name);
    
    // Set up wp home menu item
    wp_update_nav_menu_item($menu_id, 0, array(
        'menu-item-title' =>  __('WP home'),
        'menu-item-url' => '/_page-wp-to-laradmin/home',//REF:PAGE-ROUTE-URL-PRE-WP-PLUG-1
        'menu-item-status' => 'publish',
        'menu-item-type' => 'custom',
    ));
    

    // Create the toplevel templates page
    $post_title='Templates';
    $post_content='Some content';
    $postarr = array(
        'post_title'    => $post_title,
        'post_content'  => $post_content,
        'post_type'     =>'page',
        'post_status'   => 'publish',
        'post_author'   => get_current_user_id(),
        'post_parent'   =>0,
      );

    $menu_item_id=0;
    $post_id=wp_insert_post( $postarr, false);
    
    if($post_id and is_int($post_id)){
        $menu_item_id=wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-title' =>  __('Templates'),
            'menu-item-parent-id' => 0,
            'menu-item-status' => 'publish',
            'menu-item-object-id' => $post_id,
            'menu-item-object' => 'page',
            'menu-item-status' => 'publish',
            'menu-item-type' => 'post_type',
        ));

        if(!$menu_item_id){
            return 0;
        }

        // Update the Temlate page so that we can insert its proper content using its menu item id
        $postarr['ID']=$post_id; 
        $postarr['post_content']=file_get_contents(__DIR__.'/samples/templates_page.html');
        $postarr['post_content']=str_replace('{{TEMPLATES_SUBMENU_ID}}',$menu_item_id,$postarr['post_content']);
        wp_insert_post($postarr);
    }

    // We will need the these in order to add some other pages under the templates page. 
    $templates_menu_item_id=$menu_item_id;
    $templates_page_id=$post_id;

    


/************************************************************************************* */
    // Create the templates childs
    // First make a array of page details to fascilitate creating them with a loop
    $TEMPLATES_AND_PAGES=[
        ['title'=>'Index',
        'sample'=>'/samples/index.html',
        'post_parent'=>$templates_page_id,
        'menu_item_parent_id'=>$templates_menu_item_id,
        'meta'=>['scheme'=>'info',
                'linear_gradient_brand2' => 'danger',
                'linear_gradient_fainted'=>45,
            ],
        ],
        ['title'=>'Hero',
        'sample'=>'/samples/hero.html',
        'page_template'=>'page_templates/hero.blade.php',
        'thumbnail_filename'=>__DIR__.'/samples/img/example_hero_from_PAYPAL.jpg',
        'post_parent'=>$templates_page_id,
        'menu_item_parent_id'=>$templates_menu_item_id,
        'meta'=>['hero_type' => 'super'],
        ],
        ['title'=>'With sidebar',
        'sample'=>'/samples/with_sidebar.html',
        'page_template'=>'page_templates/with_sidebar.blade.php',
        'post_parent'=>$templates_page_id,
        'menu_item_parent_id'=>$templates_menu_item_id,
        ],
        ['title'=>'Three Columns',
        'sample'=>'/samples/three_col.html',
        'page_template'=>'page_templates/three_col.blade.php',
        'post_parent'=>$templates_page_id,
        'menu_item_parent_id'=>$templates_menu_item_id,
        ],
        ['title'=>'Full width',
        'sample'=>'/samples/full_width.html',
        'page_template'=>'page_templates/full_width.blade.php',
        'post_parent'=>$templates_page_id,
        'menu_item_parent_id'=>$templates_menu_item_id,
        ],
        ['title'=>'Base',
        'sample'=>'/samples/base.html',
        'page_template'=>'page_templates/base.blade.php',
        'post_parent'=>$templates_page_id,
        'menu_item_parent_id'=>$templates_menu_item_id,
        ],

        /// Independent pages
        ['title'=>'Featured image',
        'sample'=>'/samples/featured_image.html',
        'page_template'=>'page_templates/with_sidebar.blade.php',
        'thumbnail_filename'=>__DIR__.'/samples/img/gynast_from_BBC.jpg',
        'meta'=>['sidebars'=>'widgets'],
        ],
    ];

    foreach($TEMPLATES_AND_PAGES as $tpl_and_page){
        if(file_exists(__DIR__.$tpl_and_page['sample'])){//in case the file does not exists
            $post_content=file_get_contents(__DIR__.$tpl_and_page['sample']);
            $post_content=str_replace('{{TEMPLATES_SUBMENU_ID}}',$templates_menu_item_id,$post_content);
        }else{
            $post_content='Missing content!';
        }
        $postarr = array(
            'post_title'    => $tpl_and_page['title'],
            'post_content'  => $post_content,
            'post_type'     =>'page',
            'post_status'   => 'publish',
            'post_author'   => get_current_user_id(),
            'post_parent'   =>$tpl_and_page['post_parent']??0,
            'page_template' =>$tpl_and_page['page_template']??'',
            'meta_input'   => $tpl_and_page['meta']??[],
        );

        $post_id=wp_insert_post( $postarr, false);
        
        if($post_id and is_int($post_id)){

            //Check if it has thumbnail
            if(isset($tpl_and_page['thumbnail_filename']) and file_exists($tpl_and_page['thumbnail_filename'])){
                $attach_id=local_media_image($tpl_and_page['thumbnail_filename'], $post_id);
                set_post_thumbnail( $post_id ,$attach_id);
            }

            // Menu item
            $menu_item_id=wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' =>  __($tpl_and_page['title']),
                'menu-item-parent-id' => $tpl_and_page['menu_item_parent_id']??0,
                'menu-item-status' => 'publish',
                'menu-item-object-id' => $post_id,
                'menu-item-object' => 'page',
                'menu-item-status' => 'publish',
                'menu-item-type' => 'post_type',
            ));
            if(!$menu_item_id){
                return 0;
            }
        }else{
            return 0;
        }
    }



    /************************************************************************************* */
    // Custom posts
    // First make a array of custom page details to fascilitate creating them with a loop
    $CUSTOM_PTS=[
        //Page part
        ['title'=>'sidebar',
        'sample'=>'/samples/page_parts/sidebar.html',
        'post_type'=>'laradmin_page_part',
        ],
        ['title'=>'rightbar',
        'sample'=>'/samples/page_parts/rightbar.html',
        'post_type'=>'laradmin_page_part',
        ],
        ['title'=>'footer',
        'sample'=>'/samples/page_parts/footer.html',
        'post_type'=>'laradmin_page_part',
        ],
        ['title'=>'widgets',
        'sample'=>'/samples/page_parts/widgets.html',
        'post_type'=>'laradmin_page_part',
        ],
        //Homepage section
        ['title'=>'Laradmin home with WP',
        'sample'=>'/samples/home_sections/1.html',
        'post_type'=>'laradmin_home_sec',
        'menu_order'=>0,
        'thumbnail_filename'=>__DIR__.'/samples/img/home.jpg',
        'meta'=>['hero_type'=>'super'],
        ],
        ['title'=>'The middle homepage section',
        'sample'=>'/samples/home_sections/2.html',
        'post_type'=>'laradmin_home_sec',
        'menu_order'=>1,
        'meta'=>['scheme'=>'info'],
        ],
        ['title'=>'The other hompage section',
        'sample'=>'/samples/home_sections/3.html',
        'post_type'=>'laradmin_home_sec',
        'thumbnail_filename'=>__DIR__.'/samples/img/laptop_from_CONTOSO.jpg',
        'menu_order'=>2,
        ],
    ];
    foreach($CUSTOM_PTS as $custom_pt){  
        $post_title=$custom_pt['title'];
        if(file_exists(__DIR__.$custom_pt['sample'])){
            $post_content=file_get_contents(__DIR__.$custom_pt['sample']);
        }else{
            $post_content='Missing content!';
        }
        
        $postarr = array(
            'post_title'    => $post_title,
            'post_content'  => $post_content,
            'post_type'     =>$custom_pt['post_type'],
            'post_status'   => 'publish',
            'post_author'   => get_current_user_id(),
            'menu_order'    =>$custom_pt['menu_order']??'',
            'meta_input'   => $custom_pt['meta']??[],
        );

        $post_id=wp_insert_post( $postarr, false);
        
        if($post_id){
            if(isset($custom_pt['thumbnail_filename']) and file_exists($custom_pt['thumbnail_filename'])){
                $attach_id=local_media_image($custom_pt['thumbnail_filename'], $post_id);
                set_post_thumbnail( $post_id ,$attach_id);
             }
        }
        else{
            return 0;
            
        }
    }
    





    return 1;
     
}