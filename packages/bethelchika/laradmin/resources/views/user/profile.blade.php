@extends('laradmin::user.layouts.app')

@section('content')

@if($show_profile_card)
<section class="section section-primary section-first padding-top-x7 padding-bottom-x7 section-diffuse">
    <div class="container-fluid">
        @include('laradmin::user.partials.profile_board')
        
    </div>
    
</section>
@endif
<section class="section section-subtle" style="border-bottom:1px solid #ddd">
    @include('laradmin::user.partials.minor_nav',['scheme'=>'subtle','with_container'=>true,'with_icon'=>false,'left_menu_tag'=>'user_settings','root_tag'=>false])
</section>

<section class="section section-subtle">
    <div class="container-fluid">
        
            <div class="sidebar-mainbar">
                {{-- sidebar control --}}
                @include('laradmin::user.partials.sidebar.init') 
                <aside class="sidebar">
                    
                    <div class="sidebar-content mCustomScrollbar" data-mcs-theme="minimal-dark">
                        {{-- sidebar content --}}
                        <div class="sidebar-close-btn" title="Close sidebar">X</div>
                        <div class="back-to-usettings"><a href="{{route('user-settings')}}" title="Back to main settings" ><i class="fas fa-chevron-circle-left"></i> Back to main settings</a></div>
                        @include('laradmin::user.partials.quick_settings')
                        
                    </div>
                </aside>
        
                    <!-- Page Content Holder -->
                <div class="mainbar">


                       
                    
                        {{-- <h1 class="heading-2 page-title">Personal information</h1> --}}
                        
                        @include ('laradmin::inc.msg_board')
                        @include('laradmin::inc.email_confirmation_prompt')

                        <h3 id="PD-personal-information" class="heading-6 heading-slashes">Personal details</h3>
                        <div class="row row-c no-elevation">
                            <div class="col-md-2 hidden-xs">
                                <img class="img-circle" src="{{Auth::user()->avatar}}" />
                                <a class="fainted-04  text-danger" href="#" ><i class="fas fa-camera"></i></a>
                            </div>
                            <div class="col-md-10">
                                <div class="row">  
                                    <div class="col-xs-6 col-md-3"><span class="">Screen name</span></div>
                                    <div class="col-xs-6 col-md-6 ">{{Auth::user()->name}} <a class="fainted-04" href="{{route('user-edit')}}" title="Edit profile"> <i class="fas fa-pen"></i></a></div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6 col-md-3 "><span class="">First names</span></div>
                                    <div class="col-xs-6 col-md-9 ">{{Auth::user()->first_names}} <a class="fainted-04" href="{{route('user-edit')}}" title="Edit profile"> <i class="fas fa-pen"></i></a></div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6 col-md-3 "><span class="">Last name</span></div>
                                    <div class="col-xs-6 col-md-9 ">{{Auth::user()->last_name}} <a class="fainted-04" href="{{route('user-edit')}}" title="Edit profile"> <i class="fas fa-pen"></i></a></div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-6 col-md-3 "><span class="">Year of birth</span></div>
                                    <div class="col-xs-6 col-md-9 ">{{Auth::user()->year_of_birth}} <a class="fainted-04" href="{{route('user-edit')}}" title="Edit profile"> <i class="fas fa-pen"></i></a></div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6 col-md-3 "><span class="">Gender</span></div>
                                    <div class="col-xs-6 col-md-9 ">{{ucfirst(Auth::user()->gender)}} <a class="fainted-04" href="{{route('user-edit')}}" title="Edit profile"> <i class="fas fa-pen"></i></a></div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6 col-md-3 "><span class="">Faith</span></div>
                                    <div class="col-xs-6 col-md-9 ">{{ucfirst(Auth::user()->faith)}} <a class="fainted-04" href="{{route('user-edit')}}" title="Edit profile"> <i class="fas fa-pen"></i></a>
                                            
                                    </div>
                                </div>

                                @foreach($fields as $field)
                                    @if($field->group==0 or str_is($field->group,'personal'))
                                        <div class="row">
                                                <div class="col-xs-6 col-md-3 "><span class="">{{$field->label}} @if($field->unit) <em>{{$field->unit}}</em> @endif </span></div>
                                                <div class="col-xs-6 col-md-9 ">
                                                    {{$field->value}} <a class="fainted-04" href="{{route('user-edit')}}" title="Edit profile"> <i class="fas fa-pen"></i></a>     
                                                </div>
                                        </div>
                                    @endif
                                @endforeach

                                <div class="text-right padding-top-x2"><a class="btn btn-primary btn-xs" href="{{route('user-edit')}}" title="Edit profile"> <i class="fas fa-user-edit"></i> {{__('Edit profile')}}</a></div>
                            </div>
                        </div>
                            
                        
                        <h3 id="PD-contact-details" class="heading-6 heading-slashes padding-top-x8">Contact details</h3>
                        <div class="row row-c no-elevation">
                            <div class="col-xs-6 col-md-3 "><span class="">Primary e-mail</span></div>
                            <div class="col-xs-6 col-md-9 ">{{Auth::user()->email}}  
                                    <p><a class="fainted-04" href="{{route('social-user-link-email')}}"  > <i class="fas fa-pen"></i></a></p>
                                <div class="text-right padding-top-x2"><a class="btn btn-primary btn-xs"  href="{{route('social-user-link-email')}}" title="Edit profile"> <i class="fas fa-user-edit"></i> Edit e-mails</a></div>
                            </div>
                        </div>
                        

                       
                        

                        <h3 id="PD-location" class="heading-6 heading-slashes padding-top-x8">Location details</h3>

                        <div class="row row-c no-elevation">
                            <div class="col-xs-6 col-md-3 "><span class="">Country</span></div>
                            <div class="col-xs-6 col-md-9 ">@if(Auth::user()->country){{ __( 'laradmin::list_of_countries.'.Auth::user()->country )}} @endif
                                    <a class="fainted-04" href="{{route('user-edit')}}" title="Edit profile"> <i class="fas fa-pen"></i></a>
                                    <div class="text-right"><a class="btn btn-primary btn-xs" href="{{route('user-edit')}}" title="Edit profile"> <i class="fas fa-user-edit"></i> {{__('Edit profile')}}</a></div>
                            </div>
                        </div>
                        
        
                        
        
                       
                </div>
            </div>
        

    </div>
</section>
@endsection
