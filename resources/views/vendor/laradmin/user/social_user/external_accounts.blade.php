@extends('laradmin::user.layouts.app')

@section('content')
<section class="section section-primary  section-diffuse section-first section-title " style="">
    <div class="container-fluid">
        {{--  <div class="row">
            <div class="col-xs-12">
                <div class="title-box">
                    <h1 class="heading-1 content-title">{{$pageTitle}}</h1>
                    
                </div>
            </div>
        </div>  --}}
        <nav>
            <ul class="nav nav-tabs nav-flat nav-flat-md">
                <li class="title" role="presentation"><span >User settings</span></li>
                @include('laradmin::menu',['tag'=>'user_settings'])
            </ul>
        </nav>
    </div>
</section>
<section class="section section-subtle section-diffuse section-light-bg section-diffuse-no-shadow">
    <div class="container-fluid">
            
            <div class="sidebar-mainbar">
                {{-- sidebar control --}}
                @include('laradmin::user.partials.sidebar.init')
                <aside class="sidebar">
                     
                    <div class="sidebar-content mCustomScrollbar" data-mcs-theme="minimal-dark">
                        <div class="sidebar-close-btn" title="Close sidebar">X</div>
                        <div class="back-to-usettings"><a href="{{route('user-settings')}}" title="Back to main settings" ><i class="fas fa-chevron-circle-left"></i> Back to main settings</a></div>
                        {{-- sidebar content --}}
                        @include('laradmin::user.partials.quick_settings')
                        
                    </div>
                </aside>
        
                    <!-- Page Content Holder -->
                <div class="mainbar">
                        
                        @include ('laradmin::inc.msg_board')
                        @include('laradmin::inc.email_confirmation_prompt')

                        <h2 class="heading-2 ">Social and email accounts linked  to this account</h2>
                       
                        

                    
                    
                    <h3  class="heading-3 h-title title-overline" id="EA-social-user">Social accounts</h3>
                    <p class=""><a class="btn btn-default" href="{{route('social-user')}}">Social user accounts</a></p>
                    

                    <h3  class="heading-3" id="EA-email">Emails</h3>
                    <p><a href="{{route('social-user-link-email')}}"  class="btn btn-default"> Emails</a></p>
                        

                       
                        
        
                        
        
                          
                    </div>
                </div>
        

                

    </div>
</section>
@endsection
