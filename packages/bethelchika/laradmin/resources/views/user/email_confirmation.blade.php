@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.content_manager.stacks')
@section('content')
@component('laradmin::components.section',['type'=>'default','isFirst'=>true,'class'=>'first-content-padding','role'=>'main'])
        <div class="row">
            @include ('laradmin::inc.msg_board')
        
        
            @if(isset($sentEmail))
                <div class="alert alert-info">
                
                    <h4>Email address confirmation</h4>
                    <p>Confirmation email will be sent to your registered email address. Please check your email and follow the confirmation link.</p>
                    <p><a class="btn btn-primary" href="{{URL::previous()}}">Go Back</a>
                    <a class="btn btn-primary" href="{{route('user-home')}}">Go to home</a></p>
                </div>
            
            @elseif(isset($confirmed) )       
                @if($confirmed)                    
                    <div class="alert alert-success">
                        
                        <h4>Thank you!</h4> 
                        <p>Your email address is confirmed.</p>
                        <p><a class="btn btn-primary" href="{{route('user-home')}}">Go to home</a></p>
                    </div>
                @else
                    <p class="alert alert-danger">
                        Email confirmation failed. Please make another request.
                        <p><a class="btn btn-warning" href="{{route('send-email-confirmation')}}">Resend request</a></p>
                    </p>
                @endif
            @endif
    </div>
@endcomponent
            
@endsection
