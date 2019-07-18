@extends('layouts.app')
@include('laradmin::user.partials.social.metas', ['metas'=>$metas])
@include('laradmin::user.partials.content_manager.stacks')
@section('content')

<div role="banner" class="@if(!$metas['hero']['is_fullscreen']) container{{$laradmin->assetManager->isContainerFluid('-fluid')}} @endif">{{-- XCC:We wrap the section with container class (and remove it below) if we want to constrain the width of the hero--}}
    <section class="section section-{{$page->meta->scheme??'primary'}}  hero {{$laradmin->assetManager->isSuperHero('hero-super')}}  @if(str_contains($page->meta->hero_height,'full')) section-full-height @else section-extra-padding-bottom @endif @include('laradmin::user.wp.inc.section_gradient',['page'=>$page])" >
        <div class="section-overlay section-overlay-gradient-{{$page->meta->hero_shade??'default'}}">
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

@if(isset($metas['hero']['extra']) and str_word_count($metas['hero']['extra'])>5){{--The number of word count is just an arbitrary number assumed to be safe to consider content empty--}}
<section role="main" class="section section-default section-extra-padding-top section-extra-padding-bottom">     
    <div class="container{{$laradmin->assetManager->isContainerFluid('-fluid')}}">
        <div class="row">
            <div class="col-md-12">
                    {!!$metas['hero']['extra'] !!}
            </div>
        </div>
            
    </div>
</section>
@endif
@endsection