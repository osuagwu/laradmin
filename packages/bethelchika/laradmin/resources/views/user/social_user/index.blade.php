

@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.content_manager.stacks')
@section('content') 
<section class="section section-primary section-first section-title " style="">
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
                <li class="title" role="presentation"><span><a href="{{route('user-settings')}}" class="text-white">User settings</a></span></li>
                @include('laradmin::menu',['tag'=>'user_settings'])
            </ul>
        </nav>
    </div>
</section>

<section class="section section-default section-last">
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
                <div class="row  ">
                    
                    <div class="col-md-12">
                        <h1 class="heading-1 content-title">{{$pageTitle??'Edit profile'}}</h1> 

                        @include('laradmin::inc.email_confirmation_prompt')
                        @include ('laradmin::inc.msg_board')   

                        
                    
                        
                        <p class=" text-right first-content-padding">
                            <a class="btn btn-primary btn-sm" href="{{route('social-user-callout',['facebook','link'])}}"><i class="fab fa-facebook-f"></i> Link Facebook</a>
                            <a class="btn btn-primary btn-sm" href="{{route('social-user-callout',['google','link'])}}"> <i class="fab fa-google"></i> Link Google</a>
                        </p>
                        @unless(count($socialUsers))
                        <p class="text-warning">No linked social account.</p>
                        
                        @endunless
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Connected accounts</h3>
                            </div>
                            <div class="panel-body">
                                Here are a list of social accounts attached to your account.
                            </div>
                            <div class="table-responsive">
                                <table class="table  table-striped table-hover">
                                @foreach($socialUsers as $socialUser)
                                

                                    
                                    <tr>
                                        
                                        <td  class="text-primary">
                                            
                                                @if(!strcmp($socialUser->provider,'email') )
                                                <i class="fab fa-envelope-square" aria-hidden="true"></i> 
                                                @elseif(!strcmp($socialUser->provider,'facebook') )
                                                    <i class="fab fa-facebook-square" aria-hidden="true"></i>
                                                @else
                                                    <i class="fab fa-{{strtolower($socialUser->provider)}}" aria-hidden="true"></i> 
                                                @endif
                                                <strong class="hidden-sm hidden-xs">{{ucfirst($socialUser->provider)}}</strong>
                                        </td>
                                        <td> 
                                            <img class="avatar-xs" alt="{{$socialUser->social_name}}" src="{{$socialUser->social_avatar}}" /> <strong>{{$socialUser->social_name}}</strong>{{' ('.$socialUser->social_email.'), '}}<small class="hidden-sm hidden-xs faded strong">{{$socialUser->social_id }} </small>
                                        </td>
                                        
                                        <td>
                                            {{--<a style="display:none" href="#" title="Update details">Update</a>--}}
                                            <form method="post" style="display:inline" action="{{route('social-user-delete',$socialUser->id)}}">
                                                {{ method_field('DELETE') }}
                                                {{ csrf_field() }}
                                                <button title="Delete" onclick="return confirm('Are you sure you want to delete this item?')" type="submit" class="btn btn-danger" style="background-color:transparent;border:none;">
                                                    <span class="glyphicon glyphicon-remove text-muted"></span>
                                                </button>
                                                
                                            </form> 
                                        </td>
                                    </tr>
                                
                                @endforeach
                                </table>
                            </div>
                        </div>
                        
                        <br />
                        <p class=""><a href="{{route('social-user-external')}}" class="btn btn-subtle btn-sm"> <span class="glyphicon glyphicon-chevron-left"></span> Back to settings </a></p>
                        
                    </div>
            
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
