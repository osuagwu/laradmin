{{-- A simple sub-content layout with sidebar
    [YIELDS]
    @yield('sidebar)
    @yield('sub-content')
--}}
@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.content_manager.stacks')
@section('content')

@if(isset($show_profile_card) and $show_profile_card)
<section class="section section-primary padding-top-x7 padding-bottom-x7 section-diffuse">
    <div class="container-fluid">
        @include('laradmin::user.partials.profile_board')
        
    </div>
    
</section>
@endif

@if(!$laradmin->navigation->isEmptyTags('user_settings'))
<section class="section section-subtle" >
    @include('laradmin::user.partials.minor_nav',['scheme'=>'subtle','with_container'=>true,'with_icon'=>false,'left_menu_tag'=>'user_settings','root_tag'=>false])
</section>
@endif

<section class="section section-subtle">
    <div class="container-fluid">
        
            <div class="sidebar-mainbar">
                {{-- sidebar control --}}
                @include('laradmin::user.partials.sidebar.init') 
                <aside class="sidebar"  role="presentation">
                    
                    <div class="sidebar-content mCustomScrollbar" data-mcs-theme="minimal-dark">
                        {{-- sidebar content --}}
                        <div class="sidebar-close-btn" title="Close sidebar"> &times;</div>
                        {{-- <div class="back-to-usettings"><a href="{{route('user-settings')}}" title="Main settings" ><i class="fas fa-chevron-circle-left"></i> Main settings</a></div> --}}
                        
                        @yield('sidebar')
                        
                    </div>
                </aside>
        
                    <!-- Page Content Holder -->
                <div class="mainbar padding-bottom-x10" role="main" >
                    @include('laradmin::menu.breadcrumb')
                    <h1 class="heading-1 page-title strong">{{$pageTitle}}</h1> 
                    
                    @include ('laradmin::inc.msg_board')
                    @include('laradmin::inc.email_confirmation_prompt')

                       @yield('sub-content')

                </div>
            </div>   

    </div>
</section>
@endsection
