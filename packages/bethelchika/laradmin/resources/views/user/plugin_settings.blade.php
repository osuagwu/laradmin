@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.content_manager.stacks')
@section('content')
<section role="main" class="section section-default section-first section-last section-extra-padding-bottom">     
    <div class="container">
        <div class="sidebar-mainbar">
            <aside class="sidebar">
                    @include('laradmin::user.partials.profile_sidebar')
            </aside>
    
                <!-- Page Content Holder -->
            <div class="mainbar">
                    
                @include ('laradmin::inc.msg_board')
                @include('laradmin::inc.email_confirmation_prompt')

                <h1 class="heading-1 content-title">Applications</h1>
                <div class="row">
                    @unless($plugins)
                        <p>No apllication is loaded.</p>
                    @endunless
                    @foreach($plugins as $plugin)
                    <div class="col-md-4 block-content @if($loop->last) last @endif">
                        <h3 class="block-title">
                            <span class="block-icon"> <img style="width:70px" src="{{$plugin['logo']}}" /></span> 
                            <span>{{$plugin['display_name']}}</span>
                        </h3>
                        <p class="block-para"> {{$plugin['short_descr']}}</p>
                        @if(strlen($plugin['setting_route_name']))
                        <a class="block-link btn-skeleton-primary" href="{{route($plugin['setting_route_name'])}}"><i class="fa fa-edit"></i></a>
                        @endif
                    </div>
                    @endforeach

                    
                </div>
            </div>
        </div>
    </div>    
</section>


@endsection
