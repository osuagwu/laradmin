@extends('laradmin::cp.layouts.app')
@section('page-top')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('cp')}}">Control panel</a></li>
    <li class="breadcrumb-item active"></span> Settings</li>
</ol>
<h1 class='page-title'>{{$pageTitle??' '}}</h1>
@endsection

@section('content')


<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Link public disk</h3>
    </div>
    <div class="panel-body">
        @if(!file_exists(public_path().'/storage'))
            Link storage/public to public/storage to make it accessible on the www. 
            <br>
            <a class="btn btn-primary" href="{{route('cp-settings-storage-link')}}">Link public disk</a>
        
        @else 
        Public storage exists. If not, delete '{{public_path().'/storage'}}' for a chance to re-link it.
        @endif
    </div>
</div>


<p class="alert alert-warning">Please use the .env file for others settings for now !</p>
  
@endsection
