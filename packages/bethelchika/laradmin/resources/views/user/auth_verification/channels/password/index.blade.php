@extends('laradmin::user.auth_verification.sub_layouts.index')
@section('sub-content')
{{-- Note that sub-content should not include container class --}}


    <form class="form-horizontal padding-top-x10" role="form" action="{{route('user-auth-v')}}/channel/password" method="POST" >
        @csrf()
        @method('put')

        
        @include('laradmin::form.components.input_password',['name'=>'password','required'=>'required','value'=>'','help'=>'Please enter your password for verification'])  
        
        <div class="form-group">
            <div class="col-md-6 col-md-offset-4">

               
                <button type="submit" class="btn btn-primary ">
                    Continue
                </button>
            </div>
        </div>
    </form>

@endsection
