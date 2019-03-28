<?php
namespace BethelChika\Laradmin\WP;
use \Corcel\Model\Menu;
use Illuminate\Support\Collection;
use \Corcel\Model\MenuItem;
use Corcel\Model\CustomLink;
use Corcel\Model\Taxonomy;
use Corcel\Model\Post as CorcelPost;
use Corcel\Model\Page as CorcelPage;
use BethelChika\Laradmin\WP\Models\Post;
use BethelChika\Laradmin\WP\Models\Page;
//use BethelChika\Laradmin\Menu\MenuItem;
class WP{
    /*
    
    TODO: $menuitem=Corcel\Model\MenuItem::whereHas('meta', function($q)use($page){
                                                    $q->where('meta_key','_menu_item_object_id')
                                                        ->where('meta_value',$page->ID);})
                                                        ->get()->first();//Its possiblt for the page to appera in more thatn one menu whihc means that  this  result could be more than one item; but we are currently ignoring the rest and just choosing the forst one.
    if($menuitem){
        $parentid=$menuitem->meta->_menu_item_menu_item_parent;
        if($parentid){
            
        }
    }
*/

    /**
     * Exports a tag specified wp menu to laradmin navigation system
     *
     * @param string $name The name/(tag in laradmin) of the menu in wp
     * @return void
     */
    public static function exportNavigation($name='primary'){
        $menu = Menu::slug($name)->first();
        $items=$menu->items;
        $tag=$name;
       
        //Group by $items[$i]->meta->_menu_item_menu_item_parent
       $items=$items->groupBy('meta._menu_item_menu_item_parent');//Index 0 =>top level items

       // Now now export parents
       foreach($items[0] as $item_s){//
            self::exportMenuItem($item_s,$tag,$items);    
       }
    }


    /**
     * Returns the children of a wp menu item
     *
     * @param MenuItem $parent_item
     * @param Collection $items Collection of wp menuitems groupBy('parent ID')
     * @return Collection
     */
    public static function getMenuItemChildren(MenuItem $parent_item, Collection $items){
        return $items[$parent_item->ID] ?? collect([]);
    }

    /**
     * Exports a menu item and its children to laradmin navigation system
     *
     * @param CorcelMenuItem $wp_menu_item
     * @param string $tags  Tag of Menu or absolute dot separated tags of the parent menu item of laradmin navigation
     * @param string $items Collection of wp menuitems groupBy('parent ID')
     * @return void
     */
    public static function exportMenuItem(MenuItem $wp_menu_item,$tags=null,Collection $items){ 
        $item_obj=$wp_menu_item->instance();
        $url='';
        $named_route='';
        $named_route_params=[];
        switch(get_class($item_obj)){
            case CustomLink::class:
                $url=$item_obj->url;
                break;
                //
            case Taxonomy::class:
                // We do not work with Categories on the Laravel side for now
                $url='#Categories_are_not_implemented';
                return null;
                break;
            case CorcelPost::class :
                //$url='/post/'.$item_obj->post_name;
                //$url=Post::corcelMakeUrl($item_obj);
                //$url='';//route('post',$item_obj->post_name);
                $named_route='post';
                $named_route_params=[$item_obj->post_name];
                break;
            case CorcelPage::class :
                //$url='/page/'.$item_obj->post_name;
                //$url='';//route('page',$item_obj->post_name);
                $named_route='page';
                $named_route_params=[$item_obj->post_name];
                break;
            default:
                $url='#unknow_object_url';
        }
        if(isset($wp_menu_item->title) and strlen($wp_menu_item->title)){
            $name=$wp_menu_item->title;
        }else{
            $name=$item_obj->title ?? $item_obj->name ?? $item_obj->link_text ?? 'noname';
        }
        
        
        $tag=$wp_menu_item->ID;
        
        if(!$tags){
            $tags='primary';
        }
        
        app('laradmin')->navigation->create($name,$tag,$tags,
                                                    ['url'=>$url,
                                                    'namedRoute'=>$named_route,
                                                    'namedRouteParams'=>$named_route_params]);
        

        $children=self::getMenuItemChildren($wp_menu_item,$items);
        foreach($children as $child){
            
            self::exportMenuItem($child,$tags.'.'.$tag,$items);
        }
        
    }
}