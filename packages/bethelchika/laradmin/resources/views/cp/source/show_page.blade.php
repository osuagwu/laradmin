@extends('laradmin::cp.layouts.app')
@section('page-top')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('cp')}}">Control panel</a></li>
    <li class="breadcrumb-item active">Sources</li>
</ol>
<h1 class='page-title'>{{$pageTitle??' '}}</h1>
@endsection

@section('content')
<strong>Source id:</strong> {{$source_type}}



    @include('laradmin::permission.partials.ui',['source_type'=>$source_type,'source_id'=>$source_id])
    <div class="alert alert-warning"> <i class="fas fa-exclamation-triangle"></i> Note: if there is at least one permission <i>READ</i> entry for a page, the page will require login for access! i.e the entry marks the page as protected.</div>        
    <p ><a class="btn btn-default" href="{{URL::previous()}}">Back</a></p>
@endsection
