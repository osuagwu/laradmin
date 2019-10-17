@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.content_manager.stacks')
@section('content')
<section role="banner" class="section section-danger section-title  padding-top-x7 padding-bottom-x7">
    <div class="container">
        <h1 class="page-title heading-1"> <i class="fas fa-lock" ></i> {{$pageTitle}}</h1>
        <p>For security reasons, please help us verify your identity.</p>
    </div>
   
</section>
<section role="banner" class="section section-default  padding-top-x7 padding-bottom-x7">
    <div class="container">
        @include ('laradmin::inc.msg_board')
        
        
    
        @yield('sub-content')
        <br>
        <br>
        <p class="heading-6 text-center well well-sm "><a href="{{route('user-auth-v')}}">Try a different verification channel</a></p>
    </div>
   
</section>


@endsection
