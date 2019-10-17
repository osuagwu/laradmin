@extends('laradmin::user.auth_verification.sub_layouts.index')
@section('sub-content')
{{-- Note that sub-content should not include container class --}}
   
    
    @if(count($masked_emails))
        <h2 class="heading-2">Select a verification email address</h2>
        <form class="form-horizontal" role="form" action="{{route('user-auth-v')}}/channel/email" method="POST" >
            @csrf()
            

            @include('laradmin::form.components.input_radio',['name'=>'email_id', 'label'=>'Email','required'=>'required','value'=>'','options'=>$masked_emails,])  
            <div class="form-group">
                <div class="col-md-6 col-md-offset-4">

                    
                    <button type="submit" class="btn btn-primary ">
                        Continue
                    </button>
                </div>
            </div>
        </form>
    @else
        @include('laradmin::inc.email_confirmation_prompt')

    @endif
@endsection

