@extends('laradmin::user.layouts.app')

@section('content')
<section class="section section-default section-first section-last section-extra-padding-bottom">     
    <div class="container">
        <div class="sidebar-mainbar">
            <aside class="sidebar">
                    @include('laradmin::user.partials.profile_sidebar')
            </aside>
    
                <!-- Page Content Holder -->
            <div class="mainbar">
                    
                @include ('laradmin::inc.msg_board')
                @include('laradmin::inc.email_confirmation_prompt')

                <h1 class="heading-1 ">{{$pageTitle}}</h1>
                <div class="row">
                    <p>There is no setting available for this app</p>

                    
                </div>
            </div>
        </div>
    </div>    
</section>


@endsection
