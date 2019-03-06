<?php
namespace BethelChika\Comicpic\Feed;

use Illuminate\Support\Facades\Storage;
use BethelChika\ComicPic\Models\Comicpic;

class Feed{

    public static function manager(){
        return app('laradmin')->feedManager;
    }
    

    /**
     * Posts a regular feed 
     *
     * @param Comicpic $comicpic
     * @return void
     */
    public static function post(Comicpic $comicpic){
        $param['source_id']=$comicpic->id;;
        $param['source_type']=get_class($comicpic);
        $param['source_url']='/comicpic/index';
        $param['url']=('/comicpic/show/'.$comicpic->id);
        $param['image']=Storage::disk('public')->url($comicpic->medias[0]->getFullName());
        $param['share_url']=url('/comicpic/show/'.$comicpic->id);
        $param['twitter_hashtags']='comicpic,webferendum,'.str_replace('#','',$comicpic->hashtags);
        $param['twitter_screen_names']='webferendum,comicpic,'.$comicpic->twitter_screen_names;
        $param['twitter_via']=$comicpic->twitter_via?$comicpic->twitter_via:'comicpic';
        $param['lang']=$comicpic->lang;
        //$param['afterHtml']='<input />';
        //$param['beforeHtml']='<input placeholder="testiing" />'
        Feed::manager()->post($comicpic->title,$comicpic->description,'Comicpic',$param); 
    }

    

    /**
     * Deletes a post
     *
     * @param Comicpic $comicpic
     * @return int
     */
    public static function delete(Comicpic $comicpic){
        return self::manager()->destroy($comicpic->id,get_class($comicpic));
    }
   
}
