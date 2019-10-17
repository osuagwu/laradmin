
<!DOCTYPE html>
<html>
<head>

@include('comicpic::partials.item_metas')     
  


 
    {{--assests--}}
    <link href="{{ asset('vendor/comicpic/assets/css/main-plain.css') }}" rel="stylesheet">
    
            

    <!-- External styles-->
    <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js"></script>
</head>
<body class="comicpic og">
<!--Facebook social button stuff-->
@include($laradmin->theme->defaultFrom().'social.inc.facebook_js_sdk')
{{--  <div id="fb-root"></div>
    <script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.0&appId=1637625109865347&autoLogAppEvents=1';
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    </script>  --}}


    <div class="og-btns">
        <div class="fb-like" data-href="{{url('/comicpic/show/'.$comicpic->id)}}" data-width="47" data-layout="button" data-action="like" data-size="small" data-show-faces="false" data-share="true"></div>
         {{--<span title="Post to Facebook" class="social-panel-item text-muted"><i class="fab fa-facebook"></i></span> --}}
        <br /><br />
        {{-- Twitte button start--}}
        <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-text="Check this out" data-url="{{url('/comicpic/show/'.$comicpic->id)}}" data-via="webferendum" data-hashtags="comicpic,webferendum" data-related="weferendum" data-show-count="false">Tweet</a>
        {{--Twitter script should be at page footer --}}
        {{-- Twitte button end--}}
    </div>

   
     <!--Twitter Scripts-->
     <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
    </body>
    </html>