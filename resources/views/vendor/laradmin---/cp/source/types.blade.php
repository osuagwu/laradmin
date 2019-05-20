@extends('laradmin::cp.layouts.app')
@section('page-top')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('cp')}}">Control panel</a></li>
    <li class="breadcrumb-item active">Sources</li>
</ol>
<h1 class='page-title'>{{$pageTitle??' '}}</h1>
@endsection

@section('content')
<div class="text-right">
    <a class="btn btn-primary btn-sm" href="{{route('cp-source-create')}}">Create new source</a>
</div>
    <br><br>
    <ul class="list-group">
        @foreach ($source_types as $source_type)
            <li class="list-group-item"><a href="{{route('cp-source-type',[strtolower($source_type)])}}">{{$source_type}} </a></li>
        @endforeach
    </ul>
            

@endsection
