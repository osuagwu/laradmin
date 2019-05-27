<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BethelChika\Laradmin\Laradmin;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['pre-authorise']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Show the application about page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function about(Laradmin $laradmin)
    {
        $laradmin->assetManager->registerHero(null,'hero-super');

        return view('about');
    }
}
