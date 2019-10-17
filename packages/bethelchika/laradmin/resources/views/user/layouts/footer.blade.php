<footer id="footer" role="presentation">

        <div class="container{{$laradmin->assetManager->isContainerFluid('-fluid')}}" >

        @if(isset($page) and $page){!!$page->getFooter()!!} <hr> @endif

        {{-- <p class="cookies-use padding-top-x5 padding-bottom-x5">This site rely on cookies for normal functioning. By using this site, you agree we can set and use cookies.</p> --}}

        <div class="row ">
            <div class="col-xs-6 col-md-4">
                <span class="copyright">&copy; {{date('Y')}} {{ config('app.name', 'Laravel') }} </span>
            </div>

            <div class="col-xs-6 col-md-4 ">
                <nav>
                    <ul class="nav nav-pills">
                        <li role="presentation">
                            <a title="Contact us" href="{{route('contact-us-create')}}"><span class="glyphicon glyphicon-envelope"></span> Contact us</a>
                        </li>
                        <li role="presentation">
                            <a  title="Message us" href="{{route('user-message-create')}}?support=support"><span class="glyphicon glyphicon-question-sign"></span> Support</a>

                        </li>
                        <li role="presentation">
                            <a  title="Privacy" href="{{route('user-privacy')}}"><span class="glyphicon glyphicon-info-sign"></span> Privacy policy</a>

                        </li>
                    </ul>
                </nav>
            </div>

            <div class="col-xs-12 col-md-4 " >
                <nav class="social-links">
                    <ul class="nav nav-pills ">
                        @if(config('services.facebook.page_url'))
                        <li role="presentation">

                            <a href="{{config('services.facebook.page_url')}}"><i class="fab fa-facebook-f"></i></a>
                        </li>
                        @endif
                        @if(config('services.twitter.handle'))
                        <li role="presentation">
                            <a href="https://twitter.com/{{config('services.twitter.handle')}}"><i class="fab fa-twitter"></i></a>
                        </li>
                        @endif
                        @if(config('services.linkedin.url'))
                        <li role="presentation">
                            <a href="{{config('services.linkedin.url')}}"><i class="fab fa-linkedin-in"></i></a>
                        </li>
                        @endif
                        @if(config('services.youtube.url'))
                        <li role="presentation">
                            <a href="{{config('services.youtube.url')}}"><i class="fab fa-youtube"></i></a>
                        </li>
                        @endif

                    </ul>
                </nav>
            </div>
        </div>



    </div>



</footer>






