

@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.content_manager.stacks')            
@section('content')
<section class="section section-primary " style="">
    <div class="container-fluid">
        <nav>
            <ul class="nav nav-tabs nav-flat nav-flat-md">
                <li class="title" role="presentation"><span >User settings</span></li>
                @include('laradmin::menu',['tag'=>'user_settings'])
            </ul>
        </nav>
    </div>
</section>
{{--  <section class="section section-subtle section-first">
    <div class="container">
        <div class="title-box">
            <h1 class="heading-1 content-title">{{$pageTitle}}</h1>
            <div class="title-legend faded">
                Edit emails linked to this account and link new ones.            
            </div>
        </div>
    </div>
</section>  --}}
<section class="section section-subtle  section-last section-diffuse section-light-bg section-diffuse-no-shadow">
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


                        
                <h1 class="heading-1 content-title">{{$pageTitle}}</h1>
                <div class="title-legend">Emails or social media accounts linked to this account</div>                   
                
                
                <div class="row">
                    <div class="col-xs-12">              
                        @include('laradmin::inc.email_confirmation_prompt')
                        @include ('laradmin::inc.msg_board')
                                                
                        @unless(count($socialUsers))
                        <p class="text-warning">No linked emails.</p>
                        @endunless
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Emails addresses</h3>
                            </div>
                            <div class="table-responsive" >
                                <table class="table  table-striped table-hover" >
                                @foreach($socialUsers as $socialUser)
                                    <tr>
                                        
                                        <td class="">
                                            @if(!strcmp($socialUser->provider,'email') )
                                            <i class="fa fa-envelope-square" aria-hidden="true"></i> 
                                            @elseif(!strcmp($socialUser->provider,'facebook') )
                                                <i class="fab fa-facebook-square" aria-hidden="true"></i>
                                            @else
                                                <i class="fab fa-{{strtolower($socialUser->provider)}}" aria-hidden="true"></i> 
                                            @endif
                                            <span class="hidden-sm hidden-xs">{{ucfirst($socialUser->provider)}}</span>
                                            
                                        </td>
                                        <td >  
                                            <span class="">{{$socialUser->social_email}}</span>
                                        </td>
                                        <td> 
                                            @if($socialUser->status==-1)
                                                @if($socialUser->id==0)
                                                    <a class="btn btn-warning btn-xs" href="{{route('send-email-confirmation')}}">Resend confirm e-mail </a>
                                                @else
                                                    <a  class="btn btn-primary btn-xs" href="{{route('social-user-link-email-confirm-resend',$socialUser->id)}}" title="Confirm email"> Confirm</a>
                                                @endif
                                            @else
                                                <small class="label label-info"> <i class="fas fa-check"></i>  Confirmed</small>
                                            @endif 

                                            @if(!strcmp($socialUser->social_email,Auth::user()->email) )
                                            <small class="label label-info"> </span>  Primary</small> 
                                            @else 
                                                <form method="post" style="display:inline" action="{{route('social-user-link-email-set-primary',$socialUser->id)}}">
                                                    {{ method_field('PUT') }}
                                                    {{ csrf_field() }}
                                                    <button title="Set primary"  onclick="return confirm('Are you sure you want to set this email as primary?')" type="submit" class="btn btn-primary btn-xs">
                                                            <span class="glyphicon glyphicon-ok-sign"></span> Set as primary
                                                    </button>
                                                    
                                                </form> 
                                                
                                            @endif
                                            
                                        </td>
                                        <td>
                                            {{--@if(!Auth::user()->email or strcmp($socialUser->social_email,Auth::user()->email) )--}}
                                            @if($socialUser->id!=0){{--This is just the same as the one commented out above but shorter--}}
                                                <form method="post" style="display:inline" action="{{route('social-user-link-email-delete',$socialUser->id)}}">
                                                    {{ method_field('DELETE') }}
                                                    {{ csrf_field() }}
                                                    <button title="Delete"  onclick="return confirm('Are you sure you want to delete this item?')" type="submit" class="" style="background-color:transparent;border:none;">
                                                            <span class="glyphicon glyphicon-remove text-danger"></span>
                                                    </button>
                                                    
                                                </form> 
                                            @endif
                                        </td>
                                    </tr>
                                
                                @endforeach
                                </table>
                            </div>
                            
                        </div>

                        <div class="sub-content with-padding ">
                            <h3 class=" heading-3 title">Add new</h3>
                            <p>Attach new emails to your account. You willl be required to confirm each email otherwise the email will be automatically removed</p>
                            <form class="form-horizontal"  method="post" role="form"  action="{{route('social-user-link-email-create')}}" >
                                {{csrf_field()}}
                                @component('laradmin::blade_components.input_text',['name'=>'email','required'=>'required', 'value'=>'','placeholder'=>"Add new email"])
                                @endcomponent 
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        
                                        <a class="btn btn-subtle" href="{{url()->current()}}">
                                                <span class="glyphicon glyphicon-remove"></span> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                                <span class="glyphicon glyphicon-plus-sign"></span> Link email
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <p class=""><a href="{{route('social-user-external')}}" class="btn btn-default btn-sm"> <span class="glyphicon glyphicon-chevron-left"></span> Back to settings </a></p>
                                        
                                
                        
                                

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
