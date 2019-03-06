<?php
namespace BethelChika\Laradmin\Feed\Contracts;


interface DynamicFeedable{

/**
 * Get feeds
 * @param integer $limit
 * @return array Feed. Array of feed objects
 */
public function getFeeds($limit);

/**
 * Get feeds
 * @param integer $limit
 * @return string HTML formatted feed, it will be display as it is
 */
public function getFormattedFeeds($limit);

    

    
}