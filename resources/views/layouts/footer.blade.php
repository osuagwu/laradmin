<footer id="footer" >
        
        <div class="container{{$laradmin->assetManager->isContainerFluid('-fluid')}}">
            <div class="row">
                <div class="col-xs-6 col-md-4">
                    <span class="copyright">&copy; {{date('Y')}} {{ config('app.name', 'Laravel') }} </span>
                    
                </div>
    
                <div class="col-xs-6 col-md-6">
                    <nav>
                        <ul class="nav nav-pills">
                            <li role="presentation">
                                <a title="Contact us" href="{{route('contact-us-create')}}"><span class="glyphicon glyphicon-envelope"></span> Contact us</a>
                            </li>
                            <li role="presentation">
                                <a  title="Message us" href="{{route('user-message-create')}}?support=support"><span class="glyphicon glyphicon-question-sign"></span> Support</a>
                                
                            </li>
                        </ul>
                    </nav>
                    
                </div>
    
                <div class="col-xs-12 col-md-2" >
                        <nav >
                            <ul class="nav nav-pills social-icons">
                                @if(env('MEDIA_FACEBOOK_URL'))
                                <li role="presentation">
        
                                    <a href="{{env('MEDIA_FACEBOOK_URL')}}"><i class="fab fa-facebook-f"></i></a>
                                </li>
                                @endif
                                @if(env('MEDIA_TWITTER_URL'))
                                <li role="presentation">
                                    <a href="{{env('MEDIA_TWITTER_URL')}}"><i class="fab fa-twitter"></i></a>
                                </li>
                                @endif
                                @if(env('MEDIA_LINKEDIN_URL'))
                                <li role="presentation">
                                    <a href="{{env('MEDIA_LINKEDIN_URL')}}"><i class="fab fa-linkedin-in"></i></a>
                                </li>
                                @endif
                                @if(env('MEDIA_YOUTUBE_URL'))
                                <li role="presentation">
                                    <a href="{{env('MEDIA_YOUTUBE_URL')}}"><i class="fab fa-youtube"></i></a>
                                </li>
                                @endif
                                
                            </ul>
                        </nav>
                
                </div>
            </div>
            @if(isset($page)){!!$page->getFooter()!!} @endif
            <p class="cookies-use">This site rely on cookies for normal functioning. By using this site, you agree we can set and use cookies.</p>
    
        </div>
     
     
     
     </footer>
     
     
     
     
     
     
     