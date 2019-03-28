<?php

namespace BethelChika\Comicpic\Http\Controllers;

use BethelChika\Laradmin\Laradmin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(Laradmin $laradmin){
        $laradmin->assetManager->registerBodyClass('comicpic');
        $laradmin->assetManager->registerBodyClass('main-nav-no-border-bottom');
        $appname=Cache::get('comicpic.appname','Comicpic');
        $laradmin->contentManager->registerSubAppName($appname,route('comicpic.index'));
    }
    
}
