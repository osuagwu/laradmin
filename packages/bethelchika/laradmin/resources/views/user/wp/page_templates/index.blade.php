@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.social.metas', ['metas'=>$metas])
@section('content')
@if(!str_is(strtolower($page->meta->minor_nav),'off'))
<section class="section section-subtle">
    @include('laradmin::user.partials.minor_nav',['scheme'=>$page->meta->minor_nav_scheme,'with_icon'=>false,'title'=>''])
</section>
@endif
<section class="section section-default   ">
    <div class="container{{$laradmin->assetManager->isContainerFluid('-fluid')}}">
        <div class="sidebar-mainbar">
        
    
                <!-- Page Content Holder -->
            <div class="mainbar" role="main"> 
                {{--NOTE nothing is wrong with this commented code; it is an example of how to add minor nav inside mainbar  --}}
                {{-- @if(!str_is(strtolower($page->meta->minor_nav),'off'))
                    @if(isset($has_page_family) and $has_page_family)
                        @include('laradmin::user.partials.minor_nav',['scheme'=>$page->meta->minor_nav_scheme])
                    
                    @endif
                @endif --}}


                <div class="row">
                    <div class="@if(str_contains(strtolower($page->meta->rightbar),'on') or str_contains(strtolower($page->meta->blog_listing),'right')) col-md-9  @else col-md-8 col-md-offset-2 @endif">
                        <div class="left">
                            <article class="page" role="presentation">
                                <header>
                                    <h1 class="heading-huge page-title ">{{$page->title}}</h1>
                                    
                                   
                                </header>
                                
                                <div class="article-body">
                                    @include ('laradmin::inc.msg_board')
                                    @if($page->image)
                                    <div class="featured-image-box">
                                        <img class="featured-image" src="{{$page->image}}" alt="{{$page->title}}">
                                    </div>
                                    @endif

                                     
                                    
                                    {!!$page->contentFiltered!!}
                                </div>

                                <div class=" article-footer padding-top-x10 padding-bottom-x10">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4 class="heading-4 text-gray-light">Related</h4> 
                                            <ul class="nav vertical-list bg-gray-lighter ">                            
                                                @include('laradmin::menu', ['tag' => 'page_family','layout'=>'vertical'])
                                            </ul>
                                        </div>
                                        <div class="col-md-6 text-right ">
                                            <h4 class=" heading-4 strong text-gray-light">Share</h4>
                                            
                                            @include('laradmin::user.partials.social.share',['share'=>$metas,'class'=>'with-bg'])
                                            
                                            <div class="text-gray-light">
                                                <small>Date created: <time datetime="{{$page->created_at}}">{{$page->created_at->format('l jS \\of F Y h:i:s A')}}</time></small>; 
                                                <br>
                                                <small>Last updated: {{$page->updated_at}}.</small>
                                                
                                                @can('update',$page)
                                                    <small class="fainted-09"><a class="edit-link" href="{{config('laradmin.wp_rpath')}}/wp-admin/post.php?post={{$page->ID}}&action=edit">Edit</a></small>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </article>
                        </div>
                    </div>
                    
                    @if(str_contains(strtolower($page->meta->rightbar),'on') or str_contains(strtolower($page->meta->blog_listing),'right'))
                    <aside class="col-md-3 ">
                        

                        <div class="right extra-padding-bottom">
                            @if(str_contains(strtolower($page->meta->rightbar),'on'))
                                {!!$page->getRightbar()!!} 
                            @endif
                            @if(str_contains(strtolower($page->meta->blog_listing),'right'))
                            <div class="blog-listing">
                                @if($posts->count())
                                <h3 class="heading-3 ">Latest news</h3>
                                <div class="blog-posts blog-posts-v0">
                                    @foreach($posts as $post)
                                        @include('laradmin::user.wp.partials.blog_post',['post'=>$post, 'summary'=>1,'class'=>'flat'])
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @endif
                            
                        </div>
                    </aside>
                    @endif
                </div>

            </div>
        </div>

    </div>
</section>
@if(str_contains(strtolower($page->meta->blog_listing),'bottom'))
<section class="section section-subtle   ">
    <div class="container{{$laradmin->assetManager->isContainerFluid('-fluid')}}">
        <div class="blog-listing">
            @if($posts->count())
            <h3 class="heading-3 text-center padding-bottom-x3">Blog and latest news</h3>
            <div class="blog-posts blog-posts-h0">
                @foreach($posts as $post)
                    
                    @include('laradmin::user.wp.partials.blog_post',['post'=>$post, 'summary'=>1,'class'=>''])
                    
                @endforeach
            </div>
            @endif
        </div>
    </div>
</section>
@endif
@endsection
