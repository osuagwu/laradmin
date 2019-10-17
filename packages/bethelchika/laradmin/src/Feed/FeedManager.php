<?php
namespace BethelChika\Laradmin\Feed;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use BethelChika\Laradmin\Feed\Models\Feed;


class FeedManager{//TODO: SHould convert entire class to static, this should not callers
    /**
     * The default max number of data to return
     *
     * @var integer
     */
    public $limit=1;

        /**
     * The probability in percentage of returning dynamic feed during a call
     *
     * @var integer
     */
    public static $dynamicProbability=15;

    /**
     * Fields that will be exported from feed object. This is only used for non-dynamic feeds
     *
     * @var array
     */
    public static $exportFields=['id','created_at','title','content','url','image',
    'source_name','source_icon','source_icon_type','after_html','before_html',
    'source_url','share_url','type','css_class','isDynamic','twitter_hashtags','twitter_screen_names',
    'twitter_via','lang'
    ];

    /**
     * An array implementer can store the class names of their implementation of feedables
     *
     * @var array
     */
    private $feedableNames;
    
    public function __construct(){
        $this->feedableNames=new Collection;
    }

    /**
     * Register the feedable
     *
     * @param string $feedable_name Class name of Feedable
     * @return void
     */
    public function registerFeedable($feedable_name){
        $this->feedableNames->put($feedable_name,$feedable_name);
    }

    /**
     * Register the feedable
     *
     * @param string $feedable_name Class name of Feedable
     * @return void
     */
    public function unregisterFeedable($feedable_name){
        $this->feedableNames->forget($feedable_name);
    }

    /**
     * Return all feedables registered
     *
     * @return Collection of Feedable names
     */
    public function getFeedableNames(){
        return $this->feedableNames;
    }

    /**
     * Return all feedables registered in form of feedable object
     *
     * @return Collection of \BethelChika\Laradmin\Feed\Contracts\Feedable
     */
    public function getFeedables(){
        $fos=new Collection;
        foreach ($this->getFeedableNames() as $fn)
            $fos->push(new $fn);
        return $fos;
    }
    /**
     * Gets formatted dynamic feeds. These are feeds the will be supplied by getFormattedFeeds of feedables
     *
     * @param integer $limit
     * @return \Illuminate\Support\Collection
     */
    public function getFormattedDynamicFeeds($limit=10){
        $feeds=new Collection;
       

        if($limit){
            $this->$limit=$limit;
        }

        
        $feedables=$this->getFeedables()->shuffle();//Shuffle to randomise which feedable is selected first

        if (!$feedables->count()){
            return $feeds;
        }
        
        $l=ceil($this->limit/$feedables->count());
        

        foreach($feedables as $feedable){
            $feeds=$feeds->merge($feedable->getFormattedFeeds($l));

            if($l>=$limit){
                break;
            }
        }

        return $feeds;

    }

    /**
     * Get Dynamic feeds. These are feeds that will be supplied by feedables
     *
     * @param integer $limit
     * @return array
     */
    private function getDynamicFeeds($limit=1){
        $feeds_export=[];
        /////////////////////////////////////////////////////////////////
        $prob=self::$dynamicProbability;//we only try to return something $prob% of the times else we return empty array b/c we dont want too many temp feeds which can be ads
        $r=rand (1,100);
        if($prob>=$r){
            
        }else{
            return  $feeds_export;
        }
        $feeds=new Collection;
       

        if($limit){
            $this->$limit=$limit;
        }

        
        $feedables=$this->getFeedables()->shuffle();//Shuffle to randomise which feedable is selected first

        if (!$feedables->count()){
            return $feeds_export;
        }
        
        $l=ceil($this->limit/$feedables->count());
        

        foreach($feedables as $feedable){
            $feeds=$feeds->merge($feedable->getFeeds($l));

            if($l>=$limit){
                break;
            }
        }
        
        
        foreach($feeds as $feed){
            $temp['typeString']=$feed->getTypes()[$feed->type];
            foreach(self::$exportFields as $ef){
                if(!strcmp($ef,'created_at')){
                    continue;//No date for dynamic feeds
                }else{
                    $cef=camel_case($ef);
                    switch($ef){
                        case 'title':
                        case 'content':
                        case 'summary':
                            $getter='get'.ucfirst($ef);
                            $temp[$cef] =call_user_func(array($feed,$getter));
                            break;
                        default:
                            $temp[$cef]=$feed->$cef; 
                        
                    }
                    
                       
                }
                
                
            }
            $feeds_export[]=$temp;
        }


        return $feeds_export;

    }

    /**
     * Get feeds for application
     * @param $latest_timestamp Unix time stamp which when given forces one feeds created after the time is returned
     * @param integer $limit The max number of feeds to return
     * @return array
     */
    public function getFeeds($latest_timestamp=null,$limit=null){


        if(!$limit){
            $limit=$this->limit;
        }
        if($latest_timestamp){
            $c=Carbon::now()->setTimestamp($latest_timestamp);
            //get the oldest of the data newer than $latest_timestamp
            $feeds=Feed::oldest('created_at')
            ->where('created_at','>',$c)
            ->paginate($limit);
        }else{
            $feeds=Feed::latest('created_at')
            ->paginate($limit);
        }
        
        $export['hasMorePages']=$feeds->hasMorePages();
        $export['currentPage']=$feeds->currentPage();
        
        
        $feeds_export=[];
        foreach($feeds as $feed){
            $temp['typeString']=$feed->getTypes()[$feed->type];
            foreach(self::$exportFields as $ef){
                if(!strcmp($ef,'created_at')){
                    $temp[camel_case($ef)]=$feed->getDate();
                }else{
                    $temp[camel_case($ef)]=$feed->$ef;    
                }
                
                
            }
            $feeds_export[]=$temp;
        }
        //Add dynamic feed
        $dyna_feeds=$this->getDynamicFeeds();
        $feeds_export=array_merge($feeds_export,$dyna_feeds);

        
        $export['feeds']=$feeds_export;
        return $export;

    }

    /**
     * Gets a json representation of all feeds;
     *
     * @return string Json
     */
    public function getFeedsJson(){
        $feeds=$this->getFeeds();
        $dyna_feeds=$this->getDynamicFeeds();
        $feeds=array_merge($feeds,$dyna_feeds);
        
        return json_encode($feeds,JSON_UNESCAPED_SLASHES);
  
    }

     /**
     *  A static way of creating feeds
     * @param $title @see feeds migration
     * @param $content @see feeds migration
     * @param $source_name @see feeds migration
     * @param $summary @see feeds migration
     * @param $props Used to set the properties of Feed dnamically using the array key value pair where array key is the property to set. @see feeds migration for list of properties
     * @return True on boolean success
     */
    public static function post($title,$content,$source_name,$props=[]){
        $feed=new Feed();
        $feed->title=$title;
        $feed->content=$content;
        $feed->source_name=$source_name;
        

        
        $feed->set($props);
        return $feed->save();

    }

    /**
     * Deletes a feed
     *
     * @param int $source_id
     * @param string $source_type
     * @return int The number of deleted items
     */
    public function destroy($source_id,$source_type){
        
        $feeds=Feed::where('source_id',$source_id)->where('source_type',$source_type)->get();//This should not return more than one item but lets implement a general solution by assuming its a collection in case things changes
        $c=0;
        foreach($feeds as $feed){
            $feed->delete();
            $c=$c+1;
        }
        return $c;

    }

}