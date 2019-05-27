<?php

namespace  BethelChika\Laradmin\Http\Controllers\User;

use BethelChika\Laradmin\Laradmin;
use BethelChika\Laradmin\Http\Controllers\Controller; //NOTE: This is explicitly imported to avoid wrong use of a controller if this file is coppied elsewhere
class PluginUserController extends Controller
{
    public $laradmin;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
     public function __construct(Laradmin $laradmin)
     {
         parent::__construct();
         $this->middleware('auth');
         $this->laradmin=$laradmin;
         
         // Load menu items for user settings
         $this->laradmin->contentManager->loadMenu('user_settings');
         $this->laradmin->assetManager->setContainerType('fluid');
         $this->laradmin->assetManager->registerMainNavScheme('primary');
         //$laradmin->assetManager->registerBodyClass('sidebar-white');
     }
     


    /**
     * Settings for individual plugins from a pluging developer
     * @param string $viewname View name
     * @param array $data View data
     * @return \Illuminate\Http\Response
     */
    public  function pluginVendorUserView($viewname,$data=[])
    {
        //TODO: Do we not need to authorise?

        //It is needless to have sections so we first try to get content that are not inside section ignoring anything inside a section.
        $content='';
        $v=view($viewname,$data);
        $content=$v->render();

        //If there is no content thus far, we should really render the content as empty, but we can be nice and check if plugin view is using sections and render them. 
        if(!trim($content)){
            $s=$v->renderSections();// the plugin should not really be using @section as it is needless in this case, but if it does we can try to help it out
            if($s and is_array($s)){
                $content=implode('',$s);
            }
        }
    

        
       $v= view('laradmin::user.plugin.user',['content'=>$content]);
       
       return $v;
    }



}