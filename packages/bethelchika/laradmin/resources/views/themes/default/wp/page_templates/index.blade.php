@extends('laradmin::user.layouts.app')
@include($laradmin->theme->defaultFrom().'social.inc.metas', ['metas'=>$metas])
@include('laradmin::user.partials.content_manager.stacks')
@section('content')
@if(!str_is(strtolower($post->meta->minor_nav),'off'))
<section class="section section-subtle">
    @include('laradmin::user.partials.minor_nav',['scheme'=>$post->meta->minor_nav_scheme,'with_icon'=>false,'title'=>''])
</section>
@endif
<section class="section section-{{$post->meta->scheme??'default'}}  @include($laradmin->theme->defaultFrom().'wp.inc.section_gradient',['page'=>$post])">
    <div class="container{{$laradmin->assetManager->isContainerFluid('-fluid')}}">
        <div class="sidebar-mainbar">
            
            @if($post_settings['has_sidebar'])
                {{-- sidebar control --}}
                @include('laradmin::user.partials.sidebar.init')

                <aside class="sidebar" role="presentation">
                    
                    <div class="sidebar-content ">
                        {{-- sidebar content --}}
                        <div class="sidebar-close-btn" title="Close sidebar">
                            <span class="iconify" data-icon="zmdi:close" data-inline="false"></span>
                        </div>
                            
                        {{--  Start top stack  --}}
                        <div class="inner-content padding-top-x3">
                            @stack('sidebar-top')
                        </div>
                        {{--  end top stack  --}}
                        
                        
                        {!!$post->getSidebar()!!}

                        <h4 class="heading-4">In this section</h4>
                        <div class="scroll-y-lg no-scroll-x mCustomScrollbar" data-mcs-theme="minimal-dark" >
                            <div class="inner-content">
                                <ul class="nav ">                            
                                    @include('laradmin::menu', ['tag' => 'primary','layout'=>'vertical'])
                                </ul>
                            </div>
                        </div>

                        @if(str_contains(strtolower($post->meta->blog_listing),'left'))
                            @if($posts->count())
                            <h3 class="heading-3">Blog</h3>
                            <div class="scroll-y-md no-scroll-x mCustomScrollbar" data-mcs-theme="minimal-dark" >
                                <div class="inner-content">
                                    <div class="blog-listing">
                                        @foreach($posts as $post)
                                            
                                            @include($laradmin->theme->defaultFrom().'wp.partials.blog_post',['post'=>$post,'class'=>'flat'])
                                            
                                        @endforeach
                                        
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endif

                        {{--  Start bottom stack  --}}
                        <div class="inner-content padding-top-x3">
                            @stack('sidebar-bottom')
                        </div>
                        {{--  end bottom stack  --}}
                    </div>
                </aside>
            @endif
            

            <!-- Page Content Holder -->
            <div class="mainbar" role="main"> 
                {{--NOTE nothing is wrong with this commented code; it is an example of how to add minor nav inside mainbar  --}}
                {{-- @if(!str_is(strtolower($post->meta->minor_nav),'off'))
                    @if(isset($has_page_family) and $has_page_family)
                        @include('laradmin::user.partials.minor_nav',['scheme'=>$post->meta->minor_nav_scheme])
                    
                    @endif
                @endif --}}


                <div class="row">
                    <div class="@if($post_settings['has_rightbar']) col-md-9  @else col-md-8 col-md-offset-2 @endif">
                        <div class="left">
                            <article class="page" role="presentation">
                                <header>
                                    <h1 class="heading-huge page-title ">{{$post->title}}</h1>
                                    
                                   
                                </header>
                                @stack('mainbar-top')
                                
                                <div class="article-body">
                                    @include ('laradmin::inc.msg_board')
                                    @if($post->image)
                                    <div class="featured-image-box">
                                        @include($laradmin->theme->defaultFrom().'wp.partials.img_srcset',['srcset'=>$post->getFeaturedThumbSrcset(),'alt'=>$post->title, 'class'=>'featured-image','sizes'=>['(max-width: 767px) calc(100vw - 30px)','33.333vw']])
                                        {{--  <img class="featured-image" src="{{$post->getFeaturedThumb('medium')}}" alt="{{$post->title}}">  --}}
                                    </div>
                                    @endif

                                     
                                    
                                    {!!$post->contentFiltered!!}
                                </div>

                                <div class=" article-footer padding-top-x10 padding-bottom-x10">
                                    <div class="row">
                                        <div class="col-md-6">
                                            @if(isset($has_page_family) and $has_page_family)
                                            <h4 class="heading-4 text-gray-light">Related</h4> 
                                            <ul class="nav vertical-list bg-gray-lighter ">                            
                                                @include('laradmin::menu', ['tag' => 'page_family','layout'=>'vertical'])
                                            </ul>
                                            @endif
                                        </div>
                                        <div class="col-md-6 text-right ">
                                            <h4 class=" heading-4 strong text-gray-light">Share</h4>
                                            
                                            @include($laradmin->theme->defaultFrom().'social.inc.share',['share'=>$metas,'class'=>'with-bg'])
                                            
                                            @can('update',$post)
                                            <div class="text-gray-light">
                                                <small>Date created: <time datetime="{{$post->created_at}}">{{$post->created_at->format('l jS \\of F Y h:i:s A')}}</time></small>; 
                                                <br>
                                                <small>Last updated: {{$post->updated_at}}.</small>
                                                
                                                
                                                    <small class="fainted-09"><a class="edit-link" href="{{config('laradmin.wp_rpath')}}/wp-admin/post.php?post={{$post->ID}}&action=edit">Edit</a></small>
                                                
                                            </div>
                                            @endcan

                                        </div>
                                    </div>
                                    
                                </div>
                                @if(str_is($post->comment_status,'open'))
                                    @include($laradmin->theme->defaultFrom().'wp.partials.comments',['post_id'=>$post->ID])
                                @endif
                                
                                @stack('mainbar-bottom')
                                
                            {{-- <form action="{{route('post-comments')}}" method="POST">
                                @csrf
                                <input type="hidden" name="post_id" value="53">
                                <input type="hidden" name="parent_id" value="">
                                <textarea name="comment_content"></textarea>
                                <button type="submit">send</button>
                            </form> --}}

                            </article>
                        </div>
                    </div>
                    
                    @if($post_settings['has_rightbar'])
                    <aside class="col-md-3 ">
                        

                        <div class="right extra-padding-bottom">
                            {{--  Start top stack  --}}
                            @stack('rightbar-top')
                            {{--  end top stack  --}}

                            @if(str_contains(strtolower($post->meta->rightbar),'on'))
                                {!!$post->getRightbar()!!} 
                            @endif
                            @if(str_contains(strtolower($post->meta->blog_listing),'right'))
                            <div class="blog-listing">
                                @if($posts->count())
                                <h3 class="heading-3 ">Latest news</h3>
                                <div class="blog-posts blog-posts-v0">
                                    @foreach($posts as $post)
                                        @include($laradmin->theme->defaultFrom().'wp.partials.blog_post',['post'=>$post, 'summary'=>1,'class'=>'flat'])
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @endif

                            {{--  Start bottom stack  --}}
                            @stack('rightbar-bottom')
                            {{--  end bottom stack  --}}
                            
                        </div>
                    </aside>
                    @endif
                </div>

            </div>
        </div>

    </div>
</section>
@if($post_settings['has_bottom_blog_listing'])
<section class="section section-danger  @include($laradmin->theme->defaultFrom().'wp.inc.section_gradient',['page'=>null,'scheme'=>'danger','brand2'=>'success','fainted'=>96])  ">
    <div class="container{{$laradmin->assetManager->isContainerFluid('-fluid')}}">
        <div class="blog-listing">
            @if($posts->count())
            <h3 class="heading-3 text-center padding-bottom-x3">Blog and latest news</h3>
            <div class="blog-posts blog-posts-h0">
                @foreach($posts as $post)
                    
                    @include($laradmin->theme->defaultFrom().'wp.partials.blog_post',['post'=>$post, 'summary'=>1,'class'=>''])
                    
                @endforeach
            </div>
            @endif
        </div>
    </div>
</section>
@endif
@endsection
