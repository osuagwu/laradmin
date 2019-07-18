@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.social.metas', ['metas'=>$metas])
@include('laradmin::user.partials.content_manager.stacks')
@section('content')
    @if(str_is(strtolower($page->meta->minor_nav),'on'))
        @include('laradmin::user.partials.minor_nav',['scheme'=>$page->meta->minor_nav_scheme])
    @endif
    

{{-- 
    <section class="section section-default   ">
        <div class="container{{$laradmin->assetManager->isContainerFluid('-fluid')}}"> --}}
            {!!$page->contentFiltered!!}
        {{-- </div>
    </section>      --}}

    
@endsection
