@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.content_manager.stacks')
@section('content')
<section class="section section-primary section-first">
    <div class="container-fluid">
        @include('laradmin::user.partials.minor_nav',['left_menu_tag'=>'user_settings','scheme'=>'primary','title'=>'User settings','root_tag'=>false])
    </div>
</section>

<section class="section section-subtle section-light-bg section-diffuse section-diffuse-no-shadow">
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
                        @include('laradmin::menu.breadcrumb')

                        <div class="heading-1">Manage your account access and security settings</div>
                        @include ('laradmin::inc.msg_board')
                        @include('laradmin::inc.email_confirmation_prompt')
                        <nav class="nav nav-tabs nav-flat">
                                @include('laradmin::menu',['tag'=>$laradmin->navigation->getMinorNavTag()])
                        </nav>

                        <h3  class="heading-3">Second factor Authentication</h3>
                        <form class="form-inline sub-content with-padding no-elevation no-border" id="xfactor-form-update" role="form" method="POST" action="{{route('user-auth-xfactor-update')}}" >
                            @method('PUT')
                                
                            {{ csrf_field() }}

                            <div class="btn-group btn-group-xs" data-toggle="buttons">
                                <label class="btn btn-primary @if(Auth::user()->xfactor==1) active @endif">
                                    <input type="radio" name="xfactor" value="1" autocomplete="off"  @if(Auth::user()->xfactor==1) checked @endif onclick="jQuery('#xfactor-form-update').submit()"> Enable
                                </label>
                                <label class="btn btn-primary @if(Auth::user()->xfactor==0) active @endif">
                                    <input type="radio" name="xfactor" value="0"  autocomplete="off" {{!Auth::user()->xfactor?'checked':''}} onclick="document.getElementById('xfactor-form-update').submit()"> Disable
                                </label>
                            </div>
                            
                            <div class="form-group">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary btn-xs">
                                        Save
                                    </button>
                                </div>
                            </div>
                            
                    
                        </form>

                        <h3  class="heading-3" >Attempts</h3>
                        <p>You should remove any attempt that you do not recognise as it may have been made by an intruder.</p>
                        <div class="sub-content with-padding no-elevation no-border">
                            @foreach($login_attempts as $la)
                                <div class="row row-c no-elevation ">
                                        
                                    <div class="col-xs-2 text-center text-{{$la->is_success?'success':'danger'}}" style="font-size: 200%"> 
                                        <span title="{{ucfirst($la->device_type)}}">
                                            @switch(strtolower($la->device_type)) 
                                                @case('phone')
                                                    <i class="fas fa-mobile-alt"></i>
                                                    @break
                                                @case('tablet')
                                                    <i class="fas fa-tablet-alt"></i>
                                                    @break 
                                                @case('desktop')
                                                    <i class="fas fa-desktop"></i> 
                                                    @break
                                                @default
                                                    <i class="fa{{$loop->index%2?'s':'r'}} fa-question-circle" title="Unknown device"></i>
                                            @endswitch
                                        </span>
                                        
                                    </div>
                                    <div class="col-xs-8 {{$la->is_success?'text-success':'text-danger'}}"> <strong>{{$la->platform.' ' . $la->platform_version}} </strong> <small> <i> using {{$la->browser.' ' . $la->browser_version}}</i></small> <br>
                                        {{--  @if($la->city)<span class="fainted-07"> {{$la->city}},</span>@endif   
                                        @if($la->country)<span class="fainted-07"> {{$la->country}}.</span>@endif   --}}
                                        <span class="fainted-05"> 
                                            <span class="label label-{{$la->is_success?'success':'danger'}}"> {{$la->counts?:1}}</span> 
                                            {{$la->is_success?' successful ':'failed'}} attempts, @if($la->rate)made every {{round((1/$la->rate)/60)}} minutes*, @endif 
                                        </span> 
                                        <span class="fainted-04">last attempt on: {{$la->updated_at}}</span>  
                                        <br>
                                        @if($la->city or $la->country)
                                            <small class="fainted-04 text-reset">
                                                <i class="fas fa-map-marker-alt"></i> 
                                                {{$la->city??'?'}}, {{$la->country}}
                                            </small> 
                                        @endif
                                    </div>
                                    <div class="col-xs-2 {{$la->is_success?'text-success':'text-danger'}}"> 
                                            <form style="display:inline" role="form" method="POST" action="{{route('user-login-attempt',$la->id)}}" onsubmit="return confirm('Are you sure you want to delete');" >
                                                @method('delete')    
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger btn-xs">
                                                    <i class="fas fa-times"></i> Remove
                                                </button>
                                            </form>
                                    </div>
                                    
                                    
                                </div>
                            @endforeach
                            <p class="padding-top-x7 fainted-05">
                                <small class="well well-sm ">*, average time between attempts </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
