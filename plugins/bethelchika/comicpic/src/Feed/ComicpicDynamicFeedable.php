<?php
namespace BethelChika\Comicpic\Feed;

use Illuminate\Support\Collection;
use BethelChika\Laradmin\Feed\DynamicFeed as Feed;
use Illuminate\Support\Facades\Storage;
use BethelChika\Comicpic\Models\Comicpic;
use BethelChika\Laradmin\Feed\Contracts\DynamicFeedable;

class ComicpicDynamicFeedable implements DynamicFeedable{

/**
 *@inheritDoc
 */
public function getFeeds($limit){
    
    return $this->make($limit);
    
}

/**
 *@inheritDoc
 */
public function getFormattedFeeds($limit){
    
    return '<a href="/comicpic/index" >What\'s new </a>';
    
}
/**
 * Makes comic pics feeds
 *
 * @param int $limit Max number to return
 * @return array
 */
private function make($limit){
    $feeds=new Collection;
    foreach($this->fetch($limit) as $comicpic){
        $feed=new Feed($comicpic->title,$comicpic->description,'Comicpic');
        $feed->sourceUrl='/comicpic/index';
        $feed->url=('/comicpic/show/'.$comicpic->id);
        $feed->image=Storage::disk('public')->url($comicpic->medias[0]->getFullName());
        $feed->afterHtml='<a class="text-muted" href="'. $feed->sourceUrl.'">see more <i class="fas fa-angle-right"></i></a>';
        $feed->beforeHtml='<a class="text-muted" href="'. $feed->sourceUrl.'">see more <i class="fas fa-angle-right"></i></a>';
        $feeds->push($feed);
    }
    return $feeds->all();
}

/**
 * Fetches latest comic pics
 *
 * @param int $limit Max number to return
 * @return Collection
 */
private function fetch($limit){
    $comicpics=Comicpic::has('medias')->with(['medias'=>function($query){
        $query->where('tag', 'comicpic');
    },'user'])
    ->whereNotNull('published_at')->latest('published_at')
    ->limit($limit)
    ->get();
    return $comicpics;
}

       
}