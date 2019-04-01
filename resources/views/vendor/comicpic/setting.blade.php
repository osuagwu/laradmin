@extends('layouts.app')
@include('comicpic::scripts')
@section('content')
<section class="section section-primary  section-title section-diffuse section-light-bg">
    <div class="container">
        <ol class="breadcrumb bg-transparent">
            <li class="breadcrumb-item active">Comicpic</li>
        </ol>
        <h1 class="heading-3 content-title   ">Welcome to ComicPic</h1>
        <nav>
            <ul class="nav nav-tabs nav-flat">
                @include('laradmin::menu',['tag'=>'primary.comicpic'])
            </ul>
        </nav>
    </div>
</section> 
<section class="section section-subtle  section-full-height section-extra-padding-bottom section-diffuse section-light-bg ">     
    <div class="container">
        
        <div class="text-right">
            <p>
                <a href="{{route('comicpic.me')}}" class="btn btn-info btn-xs">My Comicpic</a>
                <a href="{{route('comicpic.create')}}" class="btn btn-primary btn-xs">Upload</a>
                <br /><br />
            </p>
        </div>
        
        
        @include ('laradmin::inc.msg_board')
        <p class="text-center alert alert-warning"><i class="fas fa-battery-empty"> </i> No item to display</p>
        
</section>
@endsection