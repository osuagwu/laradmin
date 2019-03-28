<?php

namespace BethelChika\Comicpic\Http\Controllers;

use Illuminate\Http\Request;
use BethelChika\Laradmin\Laradmin;
use BethelChika\Comicpic\Models\Comicpic;




class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Laradmin $laradmin)
    {
        
        parent::__construct($laradmin);
    }

    /**
     * Show the application home.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Laradmin $laradmin)
    {
        //\Illuminate\Support\Facades\DB::enableQueryLog();
        //

       //$laradmin->resetMenus();
        

        
        




        $comicpics=Comicpic::has('medias')->with(['medias'=>function($query){
            $query->where('tag', 'comicpic');
        },'user'])
        ->whereNotNull('published_at')->latest('published_at')->paginate(10);
        //$comicpics=ComicPic::with('medias')->get();
        //dd(\Illuminate\Support\Facades\DB::getQueryLog());
        
        //dd($comicpics);
        $pageTitle='Comicpic';
        return view('comicpic::index',compact('pageTitle','comicpics'));
    }
    /**
     * Show a given model.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Comicpic $comicpic,Laradmin $laradmin)
    {
        $laradmin->assetManager->unregisterBodyClass('main-nav-no-border-bottom');

        $comicpics=Comicpic::has('medias')->with(['medias'=>function($query){
            $query->where('tag', 'comicpic');
        },'user'])
        ->whereNotIn('id',[$comicpic->id])
        ->whereNotNull('published_at')->inRandomOrder()->limit(8)->get(); //TODO: Here we use inRanomOder but what we really need is to search an feach related results to the one beign shown
        
        //$laradmin->assetManager->setContainerType('fluid',true);

        $pageTitle=$comicpic->title;
        $has_small_height=$comicpic->medias[0]->getHeight()<200?true:false;
        return view('comicpic::show',compact('pageTitle','has_small_height','comicpic','comicpics'));
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

    /**
     * Open graph page for social posting
     *
     * @return \Illuminate\Http\Response
     */
    public function og(Comicpic $comicpic){
        $pageTitle=$comicpic->title;
        return view('comicpic::og',compact('pageTitle','comicpic'));
    }
}
