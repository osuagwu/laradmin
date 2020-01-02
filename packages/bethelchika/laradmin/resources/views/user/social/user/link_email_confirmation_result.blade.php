@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.content_manager.stacks')
@section('content')
@component('laradmin::components.section',['type'=>'subtle','isFirst'=>true,'isFullHeight'=>true, 'role'=>'main'])

        @slot('title')
            {{$pageTitle??'Contact us'}}
        @endslot
        
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            
            @include('laradmin::inc.email_confirmation_prompt')
            @include ('laradmin::inc.msg_board')
            
            
  
                    <p class=""><a href="{{route('social-user-link-email')}}" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> My profile</a></p>
                    
                
        </div>
             
    </div>
@endcomponent
@endsection
