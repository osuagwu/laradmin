@extends('laradmin::user.layouts.app')
@inject('laradmin','laradmin')
@section('content')
{{--
<section class="section section-primary section-light-bg section-first section-title">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 "> 
                <div class="title-box">
                    <div class="avatar-box avatar-horizontal " >
                        @if(Auth::user()->avatar)
                            <img class="img-circle pull-left" src="{{Auth::user()->avatar}}" />
                        @else 
                            <div class="avatar avatar-default">
                                <span class="avatar-text"> {{ucfirst(substr(Auth::user()->name,0,1))}}</span>
                            </div>   
                        @endif
                        <span class='avatar-legend' style="" >{{$pageTitle??'Contact us'}}</span>
                    </div>   
                </div>     
                    
            </div>
            <div class="col-md-6 ">   
                <div class="title-box">      
                    <span class="fainted-09">Registred Since</span>&nbsp;&nbsp;&nbsp;<i class="far fa-clock"></i>&nbsp; <span class="fainted-07">{{Auth::user()->created_at->todatestring()}} </span>  
                    &nbsp;&nbsp;&nbsp; <span class="fainted-03">|</span>&nbsp;&nbsp;&nbsp;
                    <span class="fainted-09">Last updated</span>&nbsp;&nbsp;&nbsp;<i class="far fa-clock"></i>&nbsp; <span class=" fainted-07">{{Auth::user()->updated_at->todatestring()}} </span>  
                </div>  
            </div>
        </div>   
    </div>
</section>
--}}
<section class="section section-subtle  section-last section-full-page">
    <div class="container-fluid">
        <div class="sidebar-mainbar">
            {{-- sidebar control --}}
            @include('laradmin::user.partials.sidebar.init')         
            <aside class="sidebar text-reset">
                
                <div class="sidebar-content mCustomScrollbar" data-mcs-theme="minimal-dark">
                    {{-- sidebar content --}}
                    <div class="sidebar-close-btn" title="Close sidebar">X</div>
                    @include('laradmin::user.partials.quick_settings')
                    
                </div>
            </aside>
    
                <!-- Page Content Holder -->
            <div class="mainbar">      
                @include ('laradmin::inc.msg_board')
                @include('laradmin::inc.email_confirmation_prompt')

                
                <div class="padding-top-x7 padding-bottom-x2 text-center text-warning">
                    <i class="fas fa-shield-alt  fainted-01 " style="font-size:3.38rem"></i>
                    @component('laradmin::blade_components.user_icon',['user'=>Auth::user(),'size'=>'lg'])
                        
                    @endcomponent
                    
                    <i class="fas fa-shield-alt text-danger fainted-01 " style="font-size:3.38rem"></i>
                    
                </div>     
                <div class="text-center  padding-top-x10">{{$pageTitle??''}}</div>

                
                <h2 class="heading-huge text-center ">Control and secure your account!</h2>
                <p  class="heading-3 text-center "> Use the settings below for modification of relevant parts of your account.</p>

                <div class=" text-center padding-bottom-x3">      
                    <strong class="fainted-09">Registred Since</strong>&nbsp;&nbsp;&nbsp;<i class="fas fa-clock"></i>&nbsp; <strong class="fainted-07">{{Auth::user()->created_at->todatestring()}} </strong>  
                    &nbsp;&nbsp;&nbsp; <span class="fainted-03">|</span>&nbsp;&nbsp;&nbsp;
                    <strong class="fainted-09">Last updated</strong>&nbsp;&nbsp;&nbsp;<i class="fas fa-clock"></i>&nbsp; <strong class=" fainted-07">{{Auth::user()->updated_at->todatestring()}} </strong>  
                </div> 

                <ul class="row list-unstyled ">
                    
                    @foreach($laradmin->navigation->getMenuByTags('user_settings')->getChildren() as $user_setting)
                        <div class="col-md-4 ">
                            <div class="sub-content  panel panel-default">
                                <h3 class="heading-1 panel-heading" style="padding-top:34px;padding-bottom:34px"><i class="{{$user_setting->iconClass}} text-primary"  style="display:inline-block; margin-right:20px;"></i> <a href="{{$user_setting->getFullLink()}}" class="text-black"> {{$user_setting->name}} <i class="fas fa-chevron-right text-reset pull-right"></i> </a></h3>
                                
                                @if($user_setting->comment or $user_setting->hasChildren())
                                <div class="panel-body" >
                                    @if($user_setting->comment)<div>{{$user_setting->comment}}</div>@endif
                                    @if($user_setting->hasChildren())
                                    <ul class="list-unstyled">
                                        @foreach($user_setting->getChildren() as $child)
                                            <li class="  padding-top-x3" > <a href="{{$child->getFullLink()}}" ><i class="{{$child->iconClass}}"></i> {{$child->name}}</a></li>
                                        @endforeach
                                    </ul>
                                    @endif
                                </div>
                                @endif
                                
                            </div>
                        </div>
                    @endforeach
                    
                </ul>
                   
            </div>
        </div>

    </div>
</section>
@endsection
