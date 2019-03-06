@extends('layouts.app')

@section('content')
@component('laradmin::blade_components.section',['type'=>'subtle','isFirst'=>true,'isFullHeight'=>true])
    <div class="row first-content-padding">
        <div class="col-md-8 col-md-offset-2">
            @include ('laradmin::inc.msg_board')

            <div class="panel panel-default">
                <div class="panel-heading">Login</div>
                <div class="panel-body">
                    
                    <div class="text-center">
                        <a class="btn btn-primary" href="{{route('social-user-callout','facebook')}}"><i class="fab fa-facebook-f"></i> Login with Facebook </a>
                        <a class="btn btn-primary" href="{{route('social-user-callout','google')}}"> <i class="fab fa-google"></i> Login with Google </a>
                    </div>
                    <hr >
                    <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>

                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    Forgot Your Password?
                                </a>
                            </div>
                        </div>
                    </form>
                    {{--
                    <hr>
                    <div class="text-center">
                        <a class="btn btn-primary" href="{{route('social-user-callout','facebook')}}"><i class="fab fa-facebook-f"></i> Login with Facebook </a>
                        <a class="btn btn-primary" href="{{route('social-user-callout','google')}}"> <i class="fab fa-google"></i> Login with Google </a>
                    </div>
                    --}}
                </div>
            </div>
        </div>
    </div>
@endcomponent
@endsection
