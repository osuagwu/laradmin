{{--
    Formatters for twitter feed
    [Input]
    $feed stdObj A twitter feed object
--}}
<div class="twitter-feed">
    <a href="https://twitter.com/{{$feed->user->screen_name}}" target="_blank">
    <img class="imgalign" src="{{$feed->user->profile_image_url_https}}">
    </a>
    <div class="tweettxts">
        <div class="tweettext">
            <span class="tweet-author-name">
                <a href="{{$feed->user->screen_name}}" target="_blank">{{$feed->user->name}}</a>
            </span>&nbsp;
            <span class="tweet-author"><a href="https://twitter.com/{{$feed->user->screen_name}}" target="_blank">{{'@'.$feed->user->screen_name}}</a></span>
            <br>
            
            {!!\BethelChika\Laradmin\Social\Feed\TwitterTextFormatter::format_text($feed)!!}
            
            @if(isset($feed->entities->media))
                <div class="media-img-box">
                    <a href="https://twitter.com/{{$feed->user->screen_name}}/status/{{$feed->id}}" target="_blank">
                        <img  src="{{$feed->entities->media[0]->media_url}}" width='100%' />
                    </a>
                </div>
            @endif 
                       
        </div> 
        <div class="tweetlink">
            <a href="https://twitter.com/{{$feed->user->screen_name}}/status/{{$feed->id}}" target="_blank">{{\Carbon\Carbon::instance($feed->normalizedDate)->format('M y')}}</a>
            <a href="https://twitter.com/intent/tweet?in_reply_to={{$feed->id}}" target="_blank">reply </a>
            <a href="https://twitter.com/intent/retweet?tweet_id={{$feed->id}}" target="_blank">retweet: {{$feed->retweet_count}}</a>
            <a href="https://twitter.com/intent/favorite?tweet_id={{$feed->id}}" target="_blank">favorite: {{$feed->favorite_count}}</a>
            <a href="https://twitter.com/{{$feed->user->screen_name}}" target="_blank"> {{\Carbon\Carbon::instance($feed->normalizedDate)->diffForHumans()}}</a>
        </div>
    </div>
</div>


