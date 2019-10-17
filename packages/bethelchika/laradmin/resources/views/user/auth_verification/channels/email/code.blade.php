@extends('laradmin::user.auth_verification.sub_layouts.index')
@section('sub-content')
{{-- Note that sub-content should not include container class --}}
   
    <h2 class="heading-2">Enter code</h2>
<p class="alert alert-info">A verification code has been sent to your email, {{$masked_email}}. Please enter the verification code below. <a class="text-warning" href="{{route('user-auth-v').'/channel/email'}}">Resend the code.</a></p>
    <form class="form-horizontal" role="form" action="{{route('user-auth-v')}}/channel/email" method="POST" >
        @csrf()
        @method('put')
        
        <input type="hidden" name="email_id" value="{{$email_id}}" >

        @include('laradmin::form.components.input_text',['name'=>'code','required'=>'required','value'=>'','help'=>'Enter the code sent to '.$masked_email])  
        <div class="form-group">
            <div class="col-md-6 col-md-offset-4 ">

                
                <button type="submit" class="btn btn-primary ">
                    Verify
                </button>
            </div>
        </div>
    </form>
   
@endsection

