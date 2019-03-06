# Feed
Feed is managed by FeedManager which can obtained thus:
```php
$feedmanager=app('laradmin')->feedManager;
```
## Posting a regular feed
To post a feed call the static post method of feed manager:
```php
$param['source_url']='/comicpic/index';
$param['url']=('/comicpic/show/'.$comicpic->id);
$param['image']='image.jpg';
$param['share_url']=url('/comicpic/show/'.$comicpic->id);
$param['afterHtml']='<span class"label label-info">Test span</span>';
$param['beforeHtml']='<input placeholder="testiing" />'
$feedmanager->post($title,$description,$poster_name,$param);
```
The parameter set through $param are optional.

## Posting Dynamic feeds
It is possible to post a dynamic feed which are feeds that are not save in database by the feed manager. To post a dynamic feed you need to implement the `BethelChika/Laradmin/Feed/DynamicFeedable class`. register it in a service provider. 
```php
$feedmanager->registerFeedable(ComicpicDynamicFeedable::class);
```
The implemented class has getFeeds() which should return an array of instance/s of `BethelChika/Laradmin/Feed/DynamicFeed` class
```php
class ComicpicDynamicFeedable implements BethelChika/Laradmin/Feed/Contracts/DynamicFeedable {
    /**
    *@inheritDoc
    */
    public function getFeeds($limit){
        $feed=new BethelChika/Laradmin/Feed/DynamicFeed($title,$description,$poster_name);
        $feed->sourceLink='/comicpic/index';
        $feed->link=('/comicpic/show/1');
        $feed->image='/image.png';
        $feed->afterHtml='<i class="label label-info">Dynamic feed</i>';
        $feed->beforeHtml='<strong class="label label-warning">Comicpic</strong>Dynamic feed" ';
        return [$feeds];
    }
}

```
## Rendering Feeds
You can use the getFeeds() method of the $feedmanager to return array of feeds.
```php
$feeds=$feedmanager->getFeeds();//
$feeds=$feedmanager->getFeeds($latest_timestamp);// Get only feeds posted after the given timestamp
$feeds=$feedmanager->getFeeds(null,$limit);// Set a limit to the number of feeds returned
```
### Blade include
The blade renderer requires Vue. It also require that two stacks are defined  and named 'head-styles' and 'footer-scripts-after-library' within the mother template. These are alreaady present in Laradmin user template.  The 'head-styles' is placed at the head section of HTML while the 'footer-scripts-after-library' is placed so that it is loaded after Vue but before Vue is initialised. The laradmin user template has already set this correctly.
To render in blade just inlude the partial thus: 
```php
@include('laradmin::user.partials.feed.feeds',['allow_fetch_on_scroll'=>'false'])
```
and set allow_fetch_on_scroll=true if you want more older feeds to be loaded as you scroll to the ottomof the page.

The feed css is located at : 'vendor/laradmin/user/css/feed/feed.css'
### Render Configurations
There a number pf parameters for rendering control, please see the above blade partial and the asset located at 'vendor/laradmin/user/js/feed/components/feed.js'.
