<?php

namespace  BethelChika\Laradmin\Http\Controllers\CP;

use Illuminate\Http\Request;
use BethelChika\Laradmin\User;
use Illuminate\Support\Facades\Gate;
use BethelChika\Laradmin\Http\Controllers\CP\Controller; //NOTE: This is explicitly imported to avoid wrong use of a controller if this file is coppied elsewhere

class ControlPanelController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->cpAuthorize();
        $pageTitle="Control panel";
        return view('laradmin::cp.index',['pageTitle'=>$pageTitle]);
    }

     
    /**
     * Help for control panel
     *
     * @return \Illuminate\Http\Response
     */
    public function help(){
        $pageTitle="Help";
        return view('laradmin::cp.help.index',compact('pageTitle'));
    }
    


}
