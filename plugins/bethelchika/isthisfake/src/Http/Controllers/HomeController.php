<?php

namespace BethelChika\IsThisFake\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application home.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pageTitle='Is this fake news?';
        
        return view('isthisfake::index',compact('pageTitle'));
    }

     /**
     * Show the application settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function setting()
    {
        
        $pageTitle='Settings';
        return view('isthisfake::setting',compact('pageTitle'));
    }

    /**
     * About page
     *
     * @return \Illuminate\Http\Response
     */
    public function about()
    {
        // $pageTitle='About us';
        // $bodyClasses=config('laradmin.css_classes')['body_hero'];
        // return view('about',compact('pageTitle','bodyClasses'));
    }
}
