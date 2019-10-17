@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.content_manager.stacks')
@section('content')
<section role="banner" class="section section-danger section-title  padding-top-x7 padding-bottom-x7">
    <div class="container">
        <h1 class="page-title heading-1"> <i class="fas fa-lock" ></i> {{$pageTitle}}</h1>
        <p>For security reasons, please help us verify your identity.</p>
    </div>
   
</section>
<section role="banner" class="section section-default  padding-top-x7 padding-bottom-x7">
    <div class="container">
        @include ('laradmin::inc.msg_board')
        
        <h2 class="heading-2">Select a verification channel</h2>
    <form class="form-horizontal" role="form" action="{{route('user-auth-v')}}" method="POST" >
            @csrf()
            @method('put')

        @foreach($channels as $channel)
            @include('laradmin::form.components.input_radio',['name'=>'channel','label'=>false,'required'=>'required','value'=>'','options'=>[$channel->getTag()=>$channel->getTitle()],'help'=>$channel->getDescription()])  
        @endforeach
            <div class="form-group">
                <div class="col-md-6 ">

                   
                    <button type="submit" class="btn btn-primary ">
                        Continue
                    </button>
                </div>
            </div>
        </form>
    </div>
   
</section>

@endsection
