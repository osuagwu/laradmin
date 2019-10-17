@extends('laradmin::user.layouts.app')
@include($laradmin->theme->defaultFrom().'social.inc.metas', ['metas'=>$metas])
@include('laradmin::user.partials.content_manager.stacks')
@section('content')

@include($laradmin->theme->defaultFrom().'wp.partials.hero')
@endsection