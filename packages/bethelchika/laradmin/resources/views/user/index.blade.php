@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.content_manager.stacks')
@section('content')
<section role="banner" class="section section-primary  padding-top-x7 padding-bottom-x7" style="border-top: 1px solid #fffdfd38;">
    <div class="container">
        @include('laradmin::user.partials.profile_board',['laradmin'=>$laradmin])
        
    </div>
   
</section>
<section role="banner" class="section section-primary  section-title "  >
        <div class="section-overlay">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 ">
                        
                        <nav>
                            <ul class="nav  nav-flat nav-tabs ">
                                <li class="title fainted-05" role="presentation"><span>User settings</span></li>
                                @include('laradmin::menu',['tag'=>'user_settings'])
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>


@component('laradmin::components.section',['type'=>'subtle','class'=>'extra-padding-bottom','role'=>'main'])
    
    
    <div class="row">
        <div class="col-md-8">
             <h6 class="padding-top-x7 padding-bottom-x3">Welcome
                {{--<small>You logged {{Auth::user()->current_login_at? Auth::user()->current_login_at->diffForHumans(): 'Never'}}</small> --}}
            </h6>
           <div class="sub-content with-padding no-border  no-elevation"> 
                <small>You logged {{Auth::user()->current_login_at? Auth::user()->current_login_at->diffForHumans() : 'Never'}}</small>
                <hr class="mid-rule">
                @include ('laradmin::inc.msg_board')
                @include('laradmin::inc.email_confirmation_prompt')
                
            @foreach($laradmin->feedManager->getFormattedDynamicFeeds() as $fdf)
                {!!$fdf!!}
                
            @endforeach
           </div>
            
            
        
            
            <h6 class="padding-top-x7 padding-bottom-x3">
                Programs
            </h6>
            <div class="sub-content with-padding no-border  no-elevation">
                <nav>
                    <ul class="nav nav-pills ">
                        @include('laradmin::menu',['tag'=>'user_apps'])
                    </ul>
                </nav>
                
            </div>
        
            <h6 class="padding-top-x7 padding-bottom-x3 ">
                Blog and latest news
            </h6>
            <div class="sub-content with-padding no-border no-elevation">

                <div class="blog-posts blog-posts-h0" >
                    @if($posts->count())
                        @foreach($posts as $post)
                            @include($laradmin->theme->defaultFrom().'wp.partials.blog_post',['post'=>$post,'class'=>'flat blog-post-sm v0','summary'=>1])
                        @endforeach
                    @else
                        <p class="alert alert-warning"> No post</p>
                    @endif
                </div>
            </div>



            @if(config('laradmin.social_feeds.limit'))
            <h6 class="padding-top-x7 padding-bottom-x3 ">
                    Social feeds
                </h6>
                <div class="sub-content with-padding no-border no-elevation">
                    @include($laradmin->theme->defaultFrom().'social.feed.feeds')
                    
                </div>
            @endif


           

            


            
            {{--  <div class=" row">
                <div class="col-md-4">
                    <h6 class="padding-top-x7 padding-bottom-x3 text-center">
                        <h6>Blog and latest news</h6>
                    </h6>
                    <div class="sub-content with-padding no-border no-elevation">

                        <div class="blog-listing" >
                            @if($posts->count())
                                @foreach($posts as $post)
                                    @include($laradmin->theme->defaultFrom().'wp.partials.blog_post',['post'=>$post,'class'=>'flat'])
                                @endforeach
                            @else
                                <p class="alert alert-warning"> No post</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>  --}}
                
        </div>
        <div class="col-md-4 ">
            <h6 class="padding-top-x7 padding-bottom-x3">
                Feeds
            </h6>
            <div>
                @include('laradmin::partials.feed.feeds',['allow_fetch_on_scroll'=>'false','box_class'=>'flat-design no-border'])
            </div>

            @if(config('services.facebook.page_url'))
            <h6 class="padding-top-x7 padding-bottom-x3 ">
                Facebook
            </h6>
            <div class="sub-content with-padding no-border no-elevation">
                @include($laradmin->theme->defaultFrom().'social.inc.facebook_page',['page_name'=>'Laradmin'])
                
            </div>
            @endif

        </div>
        
    </div>
    
@endcomponent

@endsection
