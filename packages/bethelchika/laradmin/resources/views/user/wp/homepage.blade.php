@extends('layouts.app')
@include('laradmin::user.partials.social.metas', ['metas'=>$metas])
@include('laradmin::user.partials.content_manager.stacks')
@section('content')
@if(isset($metas['hero']))
<div role="main" class="@if(!$metas['hero']['is_fullscreen']) container{{$laradmin->assetManager->isContainerFluid('-fluid')}} @endif">{{-- XCC:We wrap the section with container class (and remove it below) if we want to constrain the width of the hero--}}
    <section class="section section-{{$page->meta->scheme??'primary'}}  hero {{$laradmin->assetManager->isSuperHero('hero-super')}}  @if(str_contains($page->meta->hero_height,'full')) section-full-height @else section-extra-padding-bottom @endif" >
        <div class="section-overlay section-overlay-gradient-{{$page->meta->hero_shade??'smooth'}}">
                <div class="@if($metas['hero']['is_fullscreen']) container{{$laradmin->assetManager->isContainerFluid('-fluid')}} @endif">{{-- XCC:the container class is used here (instead of above)  to avoid constraining the width of the hero--}}
                <div class="hero-content-box    {{$page->meta->hero_headline_justify??'left'}}  {{$page->meta->hero_headline_align??'middle'}} " style="@if(is_numeric($page->meta->hero_height)) min-height: {{intval($page->meta->hero_height)}}vh; @endif">
                    <div class="hero-content hero-headline hero-headline-md   extra-padding-bottom @if(str_contains(strtolower($page->meta->hero_headline_shade),'on')) hero-headline-shade @endif">

                        <h1 class="hero-headline-text">
                                {!!$metas['hero']['title']!!}

                        </h1>
                        @foreach($metas['hero']['btns'] as $btn)
                            {!!$btn!!}
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endif


@foreach($hpss as $hps)
<div  class="@if(isset($metas['hero']) and !$metas['hero']['is_fullscreen']) container{{$laradmin->assetManager->isContainerFluid('-fluid')}} @endif">{{-- XCC:We wrap the section with container class (and remove it below) if we want to constrain the width of the section to match the hero above--}}
    <section role="presentation" class="section section-{{$hps->meta->scheme??'default'}}  section-extra-padding-top section-extra-padding-bottom">     
            <div class="@if((isset($metas['hero']) and $metas['hero']['is_fullscreen']) or !isset($metas['hero'])) container{{$laradmin->assetManager->isContainerFluid('-fluid')}} @endif">{{-- XCC:the container class is used here (instead of above)  to avoid constraining the width of the section; according the hero above --}}
            <div class="row">
                
                @if($hps->image)
                    <div class="col-md-6 text-center"> 
                        @include('laradmin::user.wp.partials.img_srcset',['srcset'=>$hps->getFeaturedThumbSrcset(),'alt'=>$hps->title,'sizes'=>['(max-width: 767px) calc(100vw - 30px)','33.333vw']])
                        <div class="hidden-md hidden-lg extra-padding-bottom"> </div>
                    </div>  
                    <div class="col-md-6"> 
                        {!!$hps->contentFiltered !!}
                    </div>
                @else
                    <div class="col-md-12"> 
                        {!!$hps->contentFiltered !!}
                    </div>
                @endif
                        
               
            </div>
                
        </div>
    </section>
</div>
@endforeach
@endsection