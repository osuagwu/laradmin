@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.social.metas', ['metas'=>$metas])
@include('laradmin::user.partials.content_manager.stacks')
@section('content')
{{-- <section class="section section-primary section-light-bg section-title section-first padding-top-x10 padding-bottom-x10">
    <div class="section-overlay">
        <div class="container">
            <h1 class="heading-1">{{$page->title}}</h1>
            <a class="text-white" href="/wp/wp-admin/post.php?post={{$page->ID}}&action=edit">Edit</a>
        </div>
    </div>
</section> --}}

<section role="main" class="section section-{{$page->meta->scheme??'default'}}   padding-bottom-x8 @include('laradmin::user.wp.inc.section_gradient',['page'=>$page])">
    <div class="container{{$laradmin->assetManager->isContainerFluid('-fluid')}}">
        <article class="page" role="presentation">
            <header>
                <h1 class="heading-1 page-title ">{{$page->title}}</h1>
                @if(str_is(strtolower($page->meta->social_share_top),'on'))
                <div class="social-panel">
                    @include('laradmin::user.partials.social.share',['share'=>$metas])
                </div>
                @endif
            </header>
            
            <div class="article-body">
                @include ('laradmin::inc.msg_board')
                @if($page->image)
                <div class="featured-image-box">
                    @include('laradmin::user.wp.partials.img_srcset',['srcset'=>$page->getFeaturedThumbSrcset(),'alt'=>$page->title, 'class'=>'featured-image','sizes'=>['(max-width: 767px) calc(100vw - 30px)','80vw']])
                </div>
                @endif

            
                
                {!!$page->contentFiltered!!}
            </div>
            

            <div class=" article-footer padding-top-x10">
                <div class="row">
                    <div class="col-md-6">
                        
                        <ul class="nav vertical-list bg-gray-lighter">                            
                            @include('laradmin::menu', ['tag' => 'page_family','layout'=>'vertical'])
                        </ul>
                        
                    </div>
                    <div class="col-md-6 text-right ">
                        @if(!str_is(strtolower($page->meta->social_share_bottom),'off'))
                        <h3 class="heading-4 text-gray-light">Share</h3>
                        @include('laradmin::user.partials.social.share',['share'=>$metas])
                        @endif
                        @can('update',$page)
                        <div class="text-gray-light">
                            <small>Date created: <time datetime="{{$page->created_at}}">{{$page->created_at->format('l jS \\of F Y h:i:s A')}}</time></small>; 
                            <small>Last updated: {{$page->updated_at}}</small>
                            
                                <small class="fainted-09"><a class="edit-link" href="{{config('laradmin.wp_rpath')}}/wp-admin/post.php?post={{$page->ID}}&action=edit">Edit</a></small>
                            
                        </div>
                        @endcan
                    </div>
                </div>
                
            </div>
        </article>
    </div>
</section>


@endsection
