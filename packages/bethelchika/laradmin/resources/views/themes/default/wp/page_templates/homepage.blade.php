{{--
[INPUT]
    $metas Array[optional] Which should have a ['hero'] index describing the hero section if any is present on the current page.
    $hpss Array The posts where each post is used to construct a section.
    --}}
@extends('laradmin::user.layouts.app')
@include($laradmin->theme->defaultFrom().'social.inc.metas', ['metas'=>$metas])
@include('laradmin::user.partials.content_manager.stacks')
@section('content')
@if(isset($metas['hero']))
    @include($laradmin->theme->defaultFrom().'wp.partials.hero')
@endif


@foreach($hpss as $hps)
<div  class="@if(isset($metas['hero']) and !$metas['hero']['is_fullscreen']) container{{$laradmin->assetManager->isContainerFluid('-fluid')}} @endif">{{-- XCC:We wrap the section with container class (and remove it below) if we want to constrain the width of the section to match the hero above--}}
    <section role="presentation" class="section section-{{$hps->meta->scheme??'default'}}  section-extra-padding-top section-extra-padding-bottom">     
            <div class="@if((isset($metas['hero']) and $metas['hero']['is_fullscreen']) or !isset($metas['hero'])) container{{$laradmin->assetManager->isContainerFluid('-fluid')}} @endif">{{-- XCC:the container class is used here (instead of above)  to avoid constraining the width of the section; according the hero above --}}
            <div class="row">
                
                @if($hps->image)
                    <div class="col-md-6 text-center"> 
                        @include($laradmin->theme->defaultFrom().'wp.partials.img_srcset',['srcset'=>$hps->getFeaturedThumbSrcset(),'alt'=>$hps->title,'sizes'=>['(max-width: 767px) calc(100vw - 30px)','33.333vw']])
                        <div class="hidden-md hidden-lg section-extra-padding-bottom"> </div>
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