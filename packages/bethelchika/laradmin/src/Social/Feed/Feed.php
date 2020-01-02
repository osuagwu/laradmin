<?php
namespace BethelChika\Laradmin\Social\Feed;
use RZ\MixedFeed\MixedFeed;
use RZ\MixedFeed\InstagramFeed;
use RZ\MixedFeed\TwitterFeed;
use RZ\MixedFeed\TwitterSearchFeed;
use RZ\MixedFeed\FacebookPageFeed;
use RZ\MixedFeed\GithubReleasesFeed;
use RZ\MixedFeed\GithubCommitsFeed;
class Feed{
    /**
     * Get array of feeds stdObj.
     *
     * @param integer $limit The max number of feeds to fetch
     * @return array of feeds stdObj
     */
    public static function getFeeds($limit=null){
       
        $climit=config('laradmin.social_feeds.limit');
        if(!$climit){
            return [];
        }
        $limit=$limit??$climit;

        $providers=[];  

       if(config('laradmin.social_feeds.providers.twitter')) {
            $twitter_user_id=config('services.twitter.user_id');
            $twitter_consumer_key=config('services.twitter.consumer_api_key');
            $twitter_consumer_secret=config('services.twitter.consumer_api_secret');
            $twitter_access_token=config('services.twitter.access_token');
            $twitter_access_token_secret=config('services.twitter.access_token_secret');
            
            if($twitter_user_id 
            and $twitter_consumer_key
            and $twitter_consumer_secret
            and $twitter_access_token
            and $twitter_access_token_secret){
                $providers[]= new TwitterFeed(
                    $twitter_user_id,
                    $twitter_consumer_key,
                    $twitter_consumer_secret,
                    $twitter_access_token,
                    $twitter_access_token_secret,
                    null,  // you can add a doctrine cache provider
                        true,  // exclude replies true/false
                        false, // include retweets true/false
                        true  // extended mode true/false
                );
            }
       }
        
            
        
        $feed = new MixedFeed($providers);
    
        return $feed->getItems($limit);
        // Or use canonical \RZ\MixedFeed\Canonical\FeedItem objects
        // for a better compatibility and easier templating with multiple
        // social platforms.
        //return $feed->getAsyncCanonicalItems(12);
    }


   
      
      
      
      
    
}