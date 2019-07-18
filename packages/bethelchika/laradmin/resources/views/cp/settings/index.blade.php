@extends('laradmin::cp.layouts.app')
@section('page-top')

<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('cp')}}">Control panel</a></li>
    <li class="breadcrumb-item active">Settings</li>
</ol>
<h1 class='page-title'>{{$pageTitle}}</h1>
@endsection
@section('content')
<nav class="nav nav-tabs">
    @include('laradmin::menu',['tag'=>$forms_nav_tag])
</nav>
@include('laradmin::form.index_form',['form'=>$form]) 
@endsection
