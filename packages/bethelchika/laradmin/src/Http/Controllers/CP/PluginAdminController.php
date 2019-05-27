<?php

namespace  BethelChika\Laradmin\Http\Controllers\CP;

use Lang;
use BethelChika\Laradmin\User;
use BethelChika\Laradmin\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use BethelChika\Laradmin\Notifications\Notice;
use BethelChika\Laradmin\Traits\EmailConfirmationEmail;
use BethelChika\Laradmin\Laradmin;
use BethelChika\Laradmin\Http\Controllers\CP\Controller; //NOTE: This is explicitly imported to avoid wrong use of a controller if this file is coppied elsewhere
class PluginAdminController extends Controller
{
    /*
    TODO: No policy is applied yet in this controller so their is no authorisation for corresponding table and model
    */

    
    public $laradmin;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
     public function __construct(Laradmin $laradmin)
     {
         $this->middleware('auth');
         parent::__construct();
         $this->laradmin=$laradmin;
     }
     
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->cpAuthorize();

        $pageTitle='Plugins';
        $plugins=$this->laradmin->pluginManager->all();
        
        
        return view('laradmin::cp.plugin.index',compact('pageTitle','plugins'));
       
       
    }

    /**
     * Show a plugin
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {//dd($this->laradmin);
        $this->cpAuthorize();
        $tag=urldecode($request->tag);
        
        $plugin=$this->laradmin->pluginManager->getDetails($tag);
        $pageTitle=$plugin['title'];
        //dd($tag);
        return view('laradmin::cp.plugin.show',compact('pageTitle','plugin'));
       
       
    }
      /**
     * Enable a plugin
     *
     * @return \Illuminate\Http\Response
     */
    public function enable(Request $request)
    {
        $this->cpAuthorize();
        $tag=urldecode($request->tag);
        
        $result=$this->laradmin->pluginManager->enable($tag);
        if($result==1){
            return back()->with('success','Done');
        }elseif($result==-1){
            return back()->with('warning','No action was taken');

        }else{
            return back()->with('danger','An error occured');
        }   
       
    }
        /**
     * Disable a plugin
     *
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request)
    {
        $this->cpAuthorize();
        $tag=urldecode($request->tag);
        
        $result=$this->laradmin->pluginManager->disable($tag);
        if($result==1){
            return back()->with('success','Done');
        }elseif($result==-1){
            return back()->with('warning','No action was taken');

        }else{
            return back()->with('danger','An error occured');
        }   
       
    }

        /**
     * Install  a plugin
     *
     * @return \Illuminate\Http\Response
     */
    public function install(Request $request)
    {
        $this->cpAuthorize();
        $tag=urldecode($request->tag);
        
        $result=$this->laradmin->pluginManager->install($tag);
        if($result==1){
            // return redirect()->action(
            //     '\\'.get_class().'@publish', ['tag' => $tag]
            // );
            return redirect()->route('cp-plugin-publish',['tag'=>$tag])->with('success','Installed. Now you should publish');
        }elseif($result==-1){
            return back()->with('warning','No action was taken');

        }else{
            return back()->with('danger','An error occured');
        }   
       
    }

    /**
     * Serve the form for publishing plugin
     *
     * @return \Illuminate\Http\Response
     */
    public function publishing(Request $request)
    {
        $this->cpAuthorize();
        $tag=urldecode($request->tag);
        //dd($tag);
        $plugin=$this->laradmin->pluginManager->getDetails($tag);
        $pageTitle=$plugin['title'];
        //dd($tag);
        return view('laradmin::cp.plugin.publishing',compact('pageTitle','plugin'));
       
       
    }


        /**
     * publish plugin
     *
     * @return \Illuminate\Http\Response
     */
    public function publish(Request $request)
    {// The method could be called by automatic form submission so make sure 
    // you give a error/warning messages when applicable that let user know 
    // that a process was done without their explicit knowledge
        $this->cpAuthorize();
        $tag=urldecode($request->tag);
        
        $result=$this->laradmin->pluginManager->publish($tag);
        if($result==1){
            return redirect()->route('cp-plugin',['tag'=>$tag])->with('success','Done');
        }elseif($result==-1){
            return back()->with('warning','Attempt was made to publish the plugin but no action was taken end');

        }else{
            return back()->with('danger','An error occured trying to publish plugin');
        }   
       
    }

        /**
     * Uninstall  a plugin
     *
     * @return \Illuminate\Http\Response
     */
    public function uninstall(Request $request)
    {
        $this->cpAuthorize();
        $tag=urldecode($request->tag);
        
        $result=$this->laradmin->pluginManager->uninstall($tag);
        if($result==1){
            return back()->with('success','Done');
        }elseif($result==-1){
            return back()->with('warning','No action was taken');

        }else{
            return back()->with('danger','An error occured');
        }   
       
    }

    /**
     * Form for Updating plugin 
     *
     * @return \Illuminate\Http\Response
     */
    public function updating(Request $request)
    {
        $this->cpAuthorize();
        $tag=urldecode($request->tag);
        //dd($tag);
        $result=$this->laradmin->pluginManager->updating($tag);
        if($result==1 or $result==-1){
            $plugin=$this->laradmin->pluginManager->getDetails($tag);
            $pageTitle=$plugin['title'];
            return view('laradmin::cp.plugin.updating',compact('pageTitle','plugin'));
        }elseif($result==-2){
            return back()->with('info','No update found');
        }elseif($result==-3){
            return back()->with('danger','Update cancel script is missing');

        }else{
            return back()->with('danger','An error occured');
        }   
       
    }
    /**
     * Update plugin
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->cpAuthorize();
        $tag=$request->tag;
        
        $result=$this->laradmin->pluginManager->update($tag);
        if($result==1){
            return redirect()->route('cp-plugin',['tag'=>$tag])->with('success','Done');
        }elseif($result==-1){
            return back()->with('warning','No action was taken');

        }else{
            return back()->with('danger','An error occured');
        }   
       
    }

      /**
     * Cancel Update plugin
     *
     * @return \Illuminate\Http\Response
     */
    public function updateCancel(Request $request)
    {
        $this->cpAuthorize();
        $tag=$request->tag;
        
        $result=$this->laradmin->pluginManager->updateCancel($tag);
        if($result==1){
            return redirect()->route('cp-plugin',['tag'=>$tag])->with('success','Done');
        }elseif($result==-1){
            return back()->with('warning','No action was taken');

        }else{
            return back()->with('danger','An error occured');
        }   
       
    }

    /**
     * Settings for inividual plugins from pluging developer
     * @param string $viewname View name
     * @param array $data View data
     * @return \Illuminate\Http\Response
     */
    public  function pluginVendorAdminView($viewname,$data=[])
    {
        $this->cpAuthorize();
       $v= view('laradmin::cp.plugin.admin');
       $v->with('viewname',$viewname);
       foreach($data as $key=>$d){
            $v->with($key,$d);
       }
       return $v;
    }



}