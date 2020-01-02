{{--
    Display feeds from various known feed providers
    
    [Input]
    $feeds Array[stdObj] [optional]:  An array of feed objects. If not provided template will try to load general feeds.
    $title string [optional] Title of the feeds.
    $box_class string [optional] The css class for the html box of the feeds
    $limit int [optional] The max number of feeds, only for when $feeds is not provided.
--}}
@if(config('laradmin.social_feeds.limit'))
    <div class="social-feeds {{$box_class??''}}">
        @if(isset($title))
            <div class="title">
                <h4>{{$title}}</h4>
            </div>
        @endif
        @foreach ($feeds??\BethelChika\Laradmin\Social\Feed\Feed::getFeeds($limit??null) as $feed)
            <div class="social-feed">
                @switch($feed->feedItemPlatform)
                    @case('twitter')
                        @include($laradmin->theme->defaultFrom().'social.feed.formatter.twitter',['feed'=>$feed])
                        @break
                    @case('instagram')
                    
                        @break
                    @default
                    
                @endswitch
            </div>
        @endforeach 
    </div>
@endif