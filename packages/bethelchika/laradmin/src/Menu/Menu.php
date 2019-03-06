<?php
namespace BethelChika\Laradmin\Menu;

class Menu extends NavigationItem
{
    /**
     * Location on the page for this menu item
     *
     * @var string
     */
    public $location;



    // /**
    //  * FInds a menu with given id. TODO: this function migh tnot be in use
    //  *
    //  * @param integer $id
    //  * @return Menu False on if it could not be found
    //  */
    // public static function findMenu($id){
    //     $menus=self::navigation()->getMenus();
    //     foreach($menus as $menu){
    //         $themenu=findItemIn($id,$menu);
    //         if($themenu instanceof Menu){
    //             return $themenu;
    //         }
    //     }
    //     return false;
    // }



    /**
     * Loads default menu
     *
     * @return void
     */
    public static function factory()
    {
        




    }

    /**
     * Check if item has link
     *
     * @return boolean
     */
    public function hasLink()
    {
        return false;
    }

    /**
     * Check if item has icon. 
     *
     * @return boolean Always false. 
     */
    public function hasIcon()
    {
        return false;
    }

    /**
     * 
     *
     * @see parent 
     */
    public function hasParent()
    {
        return false;
    }
     /**
     * 
     *
     * @see parent 
     */
    public  function isMenuItem(){
        return false;
    }
         /**
     * 
     *
     * @see parent 
     */
    public function isMenu(){
        return true;
    }


    /**
     * Render a specified menu. TODO: write a this function for application not depending on laravel
     * @param string $tag
     * @return @see Menu::render()
     */
    public static function render($tag)
    {
        return __class__ . ': Not implemented';


    }







}
