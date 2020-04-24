@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.content_manager.stacks')
@section('content')

@if($show_profile_card)
<section class="section section-primary padding-top-x7 padding-bottom-x7 section-diffuse">
    <div class="container-fluid">
        @include('laradmin::user.partials.profile_board')
        
    </div>
    
</section>
@endif
<section class="section section-subtle" style="border-bottom:1px solid #ddd">
    @include('laradmin::user.partials.minor_nav',['scheme'=>'subtle','with_container'=>true,'with_icon'=>false,'left_menu_tag'=>'user_settings','root_tag'=>false])
</section>

<section class="section section-subtle section-light-bg section-diffuse section-diffuse-no-shadow">
    <div class="container-fluid">
        
            <div class="sidebar-mainbar">
                {{-- sidebar control --}}
                @include('laradmin::user.partials.sidebar.init') 
                <aside class="sidebar"  role="presentation">
                    
                    <div class="sidebar-content mCustomScrollbar" data-mcs-theme="minimal-dark">
                        {{-- sidebar content --}}
                        <div class="sidebar-close-btn" title="Close sidebar">X</div>
                        <div class="back-to-usettings"><a href="{{route('user-settings')}}" title="Back to main settings" ><i class="fas fa-chevron-circle-left"></i> Back to main settings</a></div>
                        @include('laradmin::user.partials.quick_settings')
                        
                    </div>
                </aside>
        
                    <!-- Page Content Holder -->
                <div class="mainbar" role="main" >
                    @if($media_cover_photo)
                        <div class="cover-photo" style="padding-top:{{100*$media_cover_photo->getHeight()/$media_cover_photo->getWidth()}}%" ></div>
                    @endif
                    <div class="mainbar-header">
                        @include('laradmin::menu.breadcrumb')
                        <h1 class="heading-4 page-title">Welcome to profile</h1> 
                    </div>

                    @include ('laradmin::inc.msg_board')
                    @include('laradmin::inc.email_confirmation_prompt')
                    <nav>
                        <ul class="nav nav-tabs nav-flat">
                            @include('laradmin::menu',['tag'=>$forms_nav_tag])
                        </ul>
                    </nav>

                    
                    @if(str_is($form->getTag(),'personal'))
                        <div class="row row-c no-elevation">
                            <div class="col-md-2">
                                
                                @component('laradmin::components.user_icon',['user'=>Auth::user(),'size'=>'lg'])
                                @endcomponent
                                <a  href="{{route('user-avatar')}}" title="Edit avatar"><i class="fas fa-camera"></i></a>
                                <br><br>
                                @if($media_cover_photo)
                                    <a  title="Edit cover photo"  href="{{route('user-cphoto')}}" ><i class="fas fa-camera"></i> Change cover photo</a>
                                @else
                                    <a class="no-cover-photo" title="Add cover photo"  href="{{route('user-cphoto')}}" ><i class="fas fa-camera"></i> Add cover photo</a>
                                @endif
                                
                            </div>
                            <div class="col-md-10">
                                @include('laradmin::form.index_form',['form'=>$form])
                            </div>
                        </div>
                        
                    @else
                    
                        @include('laradmin::form.index_form',['form'=>$form]) 
                        @if(!$form->getEditLink())
                            <div class="text-right padding-top-x2"><a class="btn btn-primary btn-xs" href="{{route('user-profile-edit',[$form->getPack(),$form->getTag()])}}" title="Edit profile"> <i class="fas fa-user-edit"></i> {{__('Edit profile')}}</a></div>
                        @endif
                    @endif
                       
                </div>
            </div>
        

    </div>
</section>
@endsection
