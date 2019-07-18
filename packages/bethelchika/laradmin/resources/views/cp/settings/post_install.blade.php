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
            <br><br>
            <a class="btn btn-primary" href="{{route('cp-post-install-storage-link')}}">Link public disk</a>
        
        @else 
        Public storage exists. If not, delete '{{public_path().'/storage'}}' for a chance to re-link it.
        @endif
    </div>
</div>


<div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Wordpress</h3>
        </div>
        <div class="panel-body">
            <form action="{{route('cp-post-install-wpitems')}}" method="post">

            </form>
            @include('laradmin::form.edit_form',['form'=>$wpitems_form])

        </div>
  
@endsection
