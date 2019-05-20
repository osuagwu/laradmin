@extends('laradmin::cp.layouts.app')
@section('page-top')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('cp')}}">Control panel</a></li>
    <li class="breadcrumb-item active">Sources</li>
</ol>
<h1 class='page-title'>{{$pageTitle??' '}}</h1>
@endsection

@section('content')
<strong>Source type key:</strong> {{$source_type}}

@include('laradmin::permission.partials.ui',['source_type'=>$source_type,'source_name'=>$source_name])
        
<p ><a class="btn btn-default" href="{{URL::previous()}}">Back</a></p>
@endsection
