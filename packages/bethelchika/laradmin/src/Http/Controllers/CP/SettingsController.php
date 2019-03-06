<?php

namespace  BethelChika\Laradmin\Http\Controllers\CP;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use BethelChika\Laradmin\Http\Controllers\CP\Controller; //NOTE: This is explicitly imported to avoid wrong use of a controller if this file is coppied elsewhere
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
