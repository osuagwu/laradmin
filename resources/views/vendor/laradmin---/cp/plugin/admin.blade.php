@extends('laradmin::cp.layouts.app')
@section('page-top')
<ol class="breadcrumb" >
    <li class="breadcrumb-item"><a href="{{route('cp')}}">Control panel</a></li>
    <li class="breadcrumb-item active"><a href="{{route('cp-plugins')}}">Plugin  Settings</a></li>
</ol>
<h1 class='page-title'>{{$pageTitle??'Plugin setting '}}</h1>
@endsection

@section('content')

    @include($viewname)
  
@endsection
