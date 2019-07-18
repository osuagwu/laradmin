@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.content_manager.stacks')
@section('content')
{{-- <section class="section section-warning section-first section-title" style="">
    <div class="container">
        <div class="title-box">
            <div class="row">
                <div class="col-sm-12 col-md-10"> 
                    <h1 class="heading-1 content-title">{{$pageTitle}}</h1>
                </div>
                    
                
                <div class="hidden-xs hidden-sm col-md-2">
                    <span class=""><span class="glyphicon glyphicon-fire"></span></span>
                </div>
            </div>
        </div>
    </div>
</section> --}}


        
        
@unless($alerts)

<section role="banner" class="section section-warning ">
    <div class="container">
        <div class="">
            <h2 class="heading-huge text-center ">Great news</h2>
        </div>
    </div>
</section>
<section role="main" class="section section-default">
    <div class="container">
        <div class="first-content-padding">
            @include ('laradmin::inc.msg_board')
            <p class="heading-2">You have no alert.</p>
        </div>
    </div>
</section>

@else

<section role="banner" class="section section-warning" >
    <div class="container">
        <div class="">
            <h2 class="heading-huge text-center">You have {{count($alerts)}} alert(s)</h2>
        </div>
    </div>
</section>
<section role="main" class="section section-default">
        <div class="container">
            <div class="first-content-padding">
            @include ('laradmin::inc.msg_board')

            @foreach($alerts as $key=> $alert)  
            <div class="alert alert-danger">{{$alert}}
                @if(!strcmp('ALERT100',$key))
                    Resend confirmation email to my email address: <a class="btn btn-warning" href="{{route('send-email-confirmation')}}">Resend email</a>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>
 @endunless       

        

                


@endsection
