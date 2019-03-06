<!--Facebook social button stuff-->
{{--
<div id="fb-root"></div>

    <script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/{{app()->getLocale()}}/sdk.js#xfbml=1&version=v3.0&appId={{config('FACEBOOK_CLIENT_ID')}}&autoLogAppEvents=1';
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    </script>
    
--}}
<script>
window.fbAsyncInit = function() {
    FB.init({
    appId            : '{{env('FACEBOOK_CLIENT_ID')}}',
    autoLogAppEvents : true,
    xfbml            : true,
    version          : 'v3.1'
    });
};

(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/{{app()->getLocale()}}/sdk.js";//TODO: Check that the language is working, i.e it is the way facebook wants it
    fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>


<!--  Make any link inside '.social-button.facebook-share' a Facebook share dialogue-->
@push('footer-scripts')
    <script>
        $(function(){
            $('.social-button.facebook-share a').click(function(eve){
                eve.preventDefault();
                FB.ui({
                method: 'share',
                mobile_iframe: true,
                href: $(this).prop('href'),
                }, function(response){});
            })  
        })
    </script>
@endpush

 