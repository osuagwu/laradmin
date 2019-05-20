@extends('laradmin::user.layouts.app')

@section('content')
<section class="section section-primary section-first">
    <div class="container-fluid">
        @include('laradmin::user.partials.minor_nav',['left_menu_tag'=>'user_settings','scheme'=>'primary','title'=>'User settings','root_tag'=>false])
    </div>
</section>

<section class="section section-subtle section-full-page">
    <div class="container-fluid">
        <div class="sidebar-mainbar">
            {{-- sidebar control --}}
            @include('laradmin::user.partials.sidebar.init') 
            <aside class="sidebar"  role="presentation">
                
                <div class="sidebar-content mCustomScrollbar" data-mcs-theme="minimal-dark">
                    <div class="sidebar-close-btn" title="Close sidebar">X</div>
                    <div class="back-to-usettings"><a href="{{route('user-settings')}}" title="Back to main settings" ><i class="fas fa-chevron-circle-left"></i> Back to main settings</a></div>
                    {{-- sidebar content --}}
                    @include('laradmin::user.partials.quick_settings')
                    
                </div>
            </aside>
    
            <!-- Page Content Holder -->
            <div class="mainbar" role="main">
                <div class="row">
                    <div class="col-md-12">    
                        @include ('laradmin::inc.msg_board')
                        @include('laradmin::inc.email_confirmation_prompt')

                        <div class="heading-huge">Manage your account access and security settings</div>
                        <nav class="nav nav-tabs nav-flat">
                                @include('laradmin::menu',['tag'=>$laradmin->navigation->getMinorNavTag()])
                        </nav>

                        <h3  class="heading-3" id="S-password">Password</h3>
                        <div class="row row-c no-elevation">
                            <div class="col-xs-6 col-md-3 text-right"><strong>Password</strong> </div>
                            <div class="col-xs-6 col-md-9 "> 
                                <span class="fainted-03">*******</span>  <a class="fainted-04" href="{{route('user-edit-password')}}" title="Edit password"> <i class="fas fa-pen"></i></a>
                                
                            </div>
                        </div>
                        <div class="text-right"><a class="btn btn-primary btn-sm" href="{{route('user-edit-password')}}" title="Edit password"> <i class="fas fa-user-edit"></i> Edit password</a></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
