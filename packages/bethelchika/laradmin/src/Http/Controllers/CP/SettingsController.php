<?php

namespace  BethelChika\Laradmin\Http\Controllers\CP;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use BethelChika\Laradmin\Http\Controllers\CP\Controller; //NOTE: This is explicitly imported to avoid wrong use of a controller if this file is coppied elsewhere
use BethelChika\Laradmin\Form\Form;
use BethelChika\Laradmin\Form\Field;
use Illuminate\Support\Facades\Storage;
use BethelChika\Laradmin\Tools\Tools;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
     public function __construct()
     {
         $this->middleware('auth');
         parent::__construct();
     }

      /**
      * Show the profile info.
      * @param string $form_pack A form pack which actually should always be 'cp_settings'.
      * @param string $form_tag A tag of a form in the 'cp_settings' pack
      * @return \Illuminate\Http\Response
      */
    public function index($form_pack = 'cp_settings', $form_tag = 'general')
    {
        $this->cpAuthorize();


        $pageTitle = 'Settings ';

        // Get form
        $form = new Form($form_pack, $form_tag,'index');
        //$form->editLink=route('user-profile-edit',[$form_pack, $form_tag]);
        $forms_nav_tag = $form->packToMenu(route('cp-settings'),'user_settings.account');
        return view('laradmin::cp.settings.index', ['pageTitle' => $pageTitle, 'form' => $form, 'forms_nav_tag' => $forms_nav_tag]);

    }

    /**
     * Display a edit form of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function edit()
     {
        $this->cpAuthorize();
         
         $pageTitle="Settings";
         return view('laradmin::cp.settings',['pageTitle'=>$pageTitle]);
     }

     
      /**
     * Display a edit form of the post installation operation
     *
     * @return \Illuminate\Http\Response
     */
     public function postInstall(){
        $this->cpAuthorize();

        $wpitems_form=$this->getWpItemsForm();
        //dd($wpitems_form);

        $pageTitle='Post installation';
        return view('laradmin::cp.settings.post_install',compact('pageTitle','wpitems_form'));
     }

     /**
      * Just to return the wp items form so we dont have to remake the form whenever we need it
      *
      * @return Form
      */
     private function getWpItemsForm($mode='edit'){
        $wpitems_form=new Form('-','-',$mode);

        $field1=Field::make([
            'name' => 'install_in_wp',
            'type'=>Field::CHECKBOX,
            'value' =>'',
            'label' => 'Install in Wordpress:',
            'order' => 0,
            'rules' => 'required',
            'options'=>['plugin'=>'Laradmin plugin','page_templates'=>'Page Templates'],
            'editDescription'=>'You can select to copy the Laradmin Wordpress plugin and page templates into correct Wordpress folders. You should go to Wordpress and activate the plugin afterwards'
        ]);
        $wpitems_form->addField($field1);

        $field2=Field::make([
            'name' => 'force',
            'type'=>Field::CHECKBOX,
            'value' =>'',
            'label' => 'Overwrite if found',
            'order' => 0,
            'rules' => '',
            'options'=>['force'=>'Overwrite'],
            'editDescription'=>'If selected, the any of the above selected item will be overwritten if currently in existence.'
        ]);
        $wpitems_form->addField($field2);

        $wpitems_form->action=route('cp-post-install-wpitems');
        $wpitems_form->method='PUT';
        return $wpitems_form;
     }

       /**
     * The post installation operation
     *
     * @return \Illuminate\Http\Response
     */
    public function wpInstallItems(Request $request){
        $this->cpAuthorize();
        
        //Tools::installWpTemplates(true);
           
        $wpitems_form=$this->getWpItemsForm();
        $wpitems_form->getValidator($request->all())->validate();
        $force=false;
        if($request->input('force')){
            $force=true;
        }

        
        $msg=[];
        
        if(in_array('plugin',$request->input('install_in_wp'))){
            $r_plug=Tools::installWpPlugin($force);
            switch($r_plug){
                case 0:
                    $msg['danger'][]='There was error intalling plugin';
                    break;
                case 1:
                    $msg['success'][]='Plugin was installed';
                    break;
                case -1:
                    $msg['warning'][]='Plugin exists';
                    break;
                default:
                    $msg['warning'][]='Unknown error installing plugin';
            }
        }

        
        if(in_array('page_templates',$request->input('install_in_wp'))){
            $r_tpl=Tools::installWpTemplates($force);
            switch($r_tpl){
                case 0:
                    $msg['danger'][]='There was error intalling templates';
                    break;
                case 1:
                    $msg['success'][]='Templates were installed';
                    break;
                case -1:
                    $msg['warning'][]='Templates exist';
                    break;
                default:
                    $msg['warning'][]='Unknown error installing templates';
            }
        }
        return back()->withInput($request->all())->with($msg);    
     }

     /**
     * Activate storage link to public
     *
     * @return \Illuminate\Http\Response
     */
    public function storageLink()
    {
       $this->cpAuthorize();
       if(!file_exists(public_path().'/storage')){
            \Artisan::call('storage:link');
            if(file_exists(public_path().'/storage')){
                return back()->with('success','Done');
            }else{
                return back()->with('warning','Could not link public storage');
            }
        }else{
            return back()->with('warning','It seems this has already been done.');
        }
    }
  
}
