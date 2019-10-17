@extends('laradmin::user.auth_verification.sub_layouts.index')
@section('sub-content')
{{-- Note that sub-content should not include container class --}}
@if(count($answers))
    <h2 class="heading-2">Please provide answers to the following</h2>

    <form class="form-horizontal padding-top-x10" role="form" action="{{route('user-auth-v')}}/channel/security_question" method="POST" autocomplete="off" >
        @csrf()
        @method('put')

       

        {{ csrf_field() }}
        
            @foreach($answers as $answer)
                @component('laradmin::form.components.input_text',['name'=>'security_answers['.$answer->id.']','value'=>'','label'=>$answer->securityQuestion->question,'required'=>'required','placeholder'=>'*******','help'=>'Reminder: '.$answer->reminder])
                @endcomponent 
                <hr>
            @endforeach
        
          

        <div class="form-group">
            <div class="col-md-6 col-md-offset-4">

               
                <button type="submit" class="btn btn-primary ">
                    Continue
                </button>
            </div>
        </div>
    </form>
    
    @else
        <p class="alert alert-warning">You have not set security questions. <a href="{{route('user-auth-v')}}">Verify with another channel.</a></p>
    @endif
@endsection
