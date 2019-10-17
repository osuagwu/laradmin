@extends('laradmin::user.layouts.app')
@include($laradmin->theme->defaultFrom().'social.inc.metas', ['metas'=>$metas])
@include('laradmin::user.partials.content_manager.stacks')
@section('content')
    @if(str_is(strtolower($post->meta->minor_nav),'on'))
        @include('laradmin::user.partials.minor_nav',['scheme'=>$post->meta->minor_nav_scheme])
    @endif
    


    {!!$post->contentFiltered!!}
      
    
@endsection
