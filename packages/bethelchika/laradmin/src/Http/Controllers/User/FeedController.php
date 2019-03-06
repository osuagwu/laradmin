<?php

namespace BethelChika\Laradmin\Http\Controllers\User;

use Illuminate\Http\Request;
use BethelChika\Laradmin\Http\Controllers\Controller;
use BethelChika\Laradmin\Laradmin;

class FeedController extends Controller{
    /**
     * Get feeds for Json style
     *
     * @param Request $request
     * @return \Illuminate\Http\ResponseJson
     */
    public function fetch(Request $request,Laradmin $laradmin){

        $latest_timestamp=null;
        if($request->has('latest_timestamp')){
            $latest_timestamp=$request->latest_timestamp;
        }
        $feeds=$laradmin->feedManager->getFeeds($latest_timestamp);
        return response()->json($feeds,200,[],JSON_UNESCAPED_SLASHES);
    }
}
