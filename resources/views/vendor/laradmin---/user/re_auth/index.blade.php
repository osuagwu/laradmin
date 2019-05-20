
@extends('laradmin::user.layouts.app')

@section('content')
<section role="main" class="section section-warning  section-last  section-full-page section-diffuse section-light-bg section-diffuse-no-shadow">
    <div class="container">
        <div class="row extra-padding-top">
            <div class="col-md-8 col-md-offset-2">

                @component('laradmin::blade_components.panel', ['class'=>'panel-default text-reset'])
                    @slot('title')
                        {{$pageTitle??'Password confirmation'}}
                    @endslot

                    @include ('laradmin::inc.msg_board')
                    <p class="alert alert-warning"> <span class="glyphicon glyphicon-lock"> </span> The function your are attempting to access requires you to confirm your password.</p>
                    
                    
                    @if($authSocialUser)
                        <div class="text-center">
                            <a href="{{$reAuthRoute}}" class="btn btn-primary">
                                    <i class="fab fa-{{strtolower($authSocialUser->provider)}}" aria-hidden="true"></i>  Confirm with {{ucfirst($authSocialUser->provider)}}
                            </a>
                            <hr />
                        </div>
                    @else
                        @foreach(Auth::user()->socialUsers()->where('provider','!=','email')->where('status',1)->get() as $socialUser)
                            
                            @if ($loop->first)
                                <div class="text-center">
                                    <p class="help-block"> To confirm with a social media account not shown here, please logout and login again with the account.</p>                      
                            @endif
                                    <a href="{{route('social-user-callout',[$socialUser->provider,'re-auth-match'])}}" class="btn btn-primary" >
                                        <i class="fab fa-{{strtolower($socialUser->provider)}}" aria-hidden="true"></i>  Confirm with {{ucfirst($socialUser->provider)}}
                                    </a>
                            @if ($loop->last)
                                    <hr />
                                </div>  
                            @endif                      
                        @endforeach
                    @endif
                    
                    <form class="form-horizontal" role="form" method="POST" action="{{route('re-auth')}}">
                        
                        {{ csrf_field() }}

                        
                        @component('laradmin::blade_components.input_password',['name'=>'password','label'=>'Current password','required'=>'required',])
                        @endcomponent 

                        
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                
                                <a href="{{route('user-security')}}" class="btn btn-subtle">Cancel</a>
                                <button type="submit" class="btn btn-secondary">
                                    Confirm
                                </button>
                            </div>
                        </div>
                
                    </form>
                
                    
                @endcomponent    
            </div>
        </div>
    </div>
</section>
@endsection


