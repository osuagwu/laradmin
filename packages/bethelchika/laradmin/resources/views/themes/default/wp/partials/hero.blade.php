{{-- The hero template transfer the job of building a hero page to this partial.
    So it is sort of private/continuation of a hero template.
    This partial rely on all the variables sent to the hero template and 
    therefore must have access to those variable 
    
    [INPUT]
    For input see Hero template the calling controller because partial should enherite all the 
    variables the crontroller sends to the hero template. The variables include:
    $post The page object.
    $metas with a ['hero'] index  which is constructed in WPCOntroller.
--}}

@push('head-styles')
    <style>
        @media(min-width:768px){
            .section .hero-content-box{
                @if($post->meta->hero_content_width)
                    width:{{intval($post->meta->hero_content_width)}}%;
                @endif
                @if(is_numeric($post->meta->hero_height))
                    min-height: {{intval($post->meta->hero_height)}}vh;
                @endif
            } 
        }

        .section .hero-content-box{
            @if(is_numeric($post->meta->hero_height))
                min-height: {{intval($post->meta->hero_height)}}vh;
            @endif
        } 
    </style>
@endpush

<div role="banner" class="@if(!$metas['hero']['is_fullscreen']) container{{$laradmin->assetManager->isContainerFluid('-fluid')}} @endif">{{-- XCC:We wrap the section with container class (and remove it below) if we want to constrain the width of the hero --}}
    <section class="section section-{{$post->meta->scheme??'primary'}}  hero {{$laradmin->assetManager->isSuperHero('hero-super')}}  @if(str_contains($post->meta->hero_height,'full')) section-full-height @else section-extra-padding-bottom @endif @include($laradmin->theme->defaultFrom().'wp.inc.section_gradient',['page'=>$post])" >
        <div class="section-overlay section-overlay-gradient-{{$post->meta->hero_shade??'default'}}">
                <div class="@if($metas['hero']['is_fullscreen']) container{{$laradmin->assetManager->isContainerFluid('-fluid')}} @endif">{{-- XCC:the container class is used here (instead of above)  to avoid constraining the width of the hero --}}
                <div class="hero-content-box    {{$post->meta->hero_headline_justify??'left'}}  {{$post->meta->hero_headline_align??'middle'}}  @if(str_contains(strtolower($post->meta->hero_content_shade),'on')) shade @endif" >
                    <div class="hero-content hero-headline hero-headline-md   extra-padding-bottom @if(str_contains(strtolower($post->meta->hero_headline_shade),'on')) hero-headline-shade @endif">

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

@if(isset($metas['hero']['extra']) and str_word_count($metas['hero']['extra'])>5){{-- The number of word count is just an arbitrary number assumed to be safe to consider content empty --}}
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