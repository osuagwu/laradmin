@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.social.metas', ['metas'=>$metas])

@section('content')
    @if(!str_is(strtolower($page->meta->minor_nav),'off'))
        @if(isset($has_page_family) and $has_page_family)
            @include('laradmin::user.partials.minor_nav',['scheme'=>$page->meta->minor_nav_scheme])
        
        @endif
    @endif
    




    {!!$page->contentFiltered!!}
@endsection
