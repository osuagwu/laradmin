@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.content_manager.stacks')
@section('content')
<section role="banner" class="section section-primary section-first section-title">
    <div class="container">

        <nav>
            <ul class="nav nav-flat nav-tabs ">
                <li class="title" role="presentation">Account control</li>
                @include('laradmin::menu',['tag'=>'user_settings','with_icon'=>false])
            </ul>
        </nav>
    </div>
</section>
<section role="main" class="section section-default section-last">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
               {{-- <h1 class="heading-1 content-title">
                    {{$pageTitle}} 
                      <small>Suspend or delete this account</small>  
                </h1>--}}

                @include ('laradmin::inc.msg_board')
                @include('laradmin::inc.email_confirmation_prompt')  
                {{-- <p>Account controll lets deactivate your account for some time or completely delete it.</p>               --}}
                        
                <h3  class="heading-3 title-underline" >Account activation/deactivation</h3>
                <p>When you deactivate your account it will be temporarily suspended and the process should not delete your data. You should be logged out after deactivation. To reactivate your account, simply log back in. </p>
                <p>You can use the link below to activate your account</p>
                <div class="text-right">
                    @if(Auth::user()->self_deactivated_at)
                        <p ><a href="{{route('user-self-reactivate')}}" class="btn btn-primary"><strong>Reactivate my account</strong></a></p>
                    @else
                        <p class=""><a href="{{route('user-self-deactivate')}}" class="btn btn-danger btn-sm"><strong>Deactivate my account</strong></a></p>
                    @endif
                </div>

                <h3  class="heading-3 title-underline" >Account deletion</h3>
                <p>Once you account is deleted it cannot be recovered. After you initiate the deletion of your account, you will be logged out and a message will inform you of an estimated date on which you account will be permanently deleted. If you change you mind before the date, you should be able to stop the deletion by logging back in back here to cancel the deletion. Stopping the deletion is not guaranteed closer to the deletion date.</p>
                <p>You can use the link below to initiate the deletion of your account or cancel it</p>

                <div class="text-right">
                    @if(Auth::user()->self_delete_initiated_at)
                        <p class=""><a href="{{route('user-self-delete-cancel')}}" class="btn btn-primary"><strong>Cancel permanent account deletion</strong></a></p>
                    @else
                        <p class=""><a href="{{route('user-self-delete')}}" class="btn btn-danger btn-sm"><strong>Permanently delete my account</strong></a></p>
                    @endif
                </div>
                
            </div>
        </div>
    </div>
</section>
@endsection
