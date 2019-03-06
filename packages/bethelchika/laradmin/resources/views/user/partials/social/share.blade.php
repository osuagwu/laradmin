{{--
* Print the share link for a given url
* [INPUTS]
* $share['provider'] array The name provider e.g ['facebook','twitter','linkedin','whatsapp'] . If not given all providers will be printed.
* $share['url'] string The Url of the resource to be shared
* $share['tweet'] string The tweet test.
* $class string The class/es for the box {e.g 'with-bg': to add background colors to the social icons}
--}}

<div class="social-buttons {{$class??''}}">
        
            @if(!isset($share['provider'] ) or in_array('facebook',$share['provider']) )
                <div class="social-button facebook-share"><a  href="{{$share['url']}}"><span class="fab fa-facebook-f"></span></a></div>
            @endif

            @if(!isset($share['provider'] ) or in_array('twitter',$share['provider']) )
                <div class="social-button twitter-tweet"><a href="https://twitter.com/intent/tweet?text={{urlencode($share['tweet'])}}&amp;url={{$share['url']}}"  ><span class="fab fa-twitter"></span></a></div>
            @endif

            @if(!isset($share['provider'] ) or in_array('whatsapp',$share['provider']) )
                <div class="social-button whatsapp-send"><a target="_blank" href="https://wa.me/?text={{urlencode($share['tweet'].' '.$share['url'])}}"  ><span class="fab fa-whatsapp"></span></a></div>
            @endif

            @if(!isset($share['provider'] ) or in_array('linkedin',$share['provider']) )
                <div class="social-button linkedin-share-article" ><a target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{urlencode($share['url'])}}" ><span class="fab fa-linkedin-in"></span></a></div>
            @endif

            @if(!isset($share['provider']) or in_array('mail',$share['provider']))
                <div class="social-button mail-to"><a href="mailto:?subject={{urlencode($share['url'])}}"><span class="fas fa-envelope"></span></a></div>
            @endif
               
            
        


</div>
