 
@extends('laradmin::cp.layouts.app')
@section('page-top')
<ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('cp')}}">Control panel</a></li>
                <li class="breadcrumb-item "><a href="{{route('cp-user-groups')}}">User groups</a></li>
                <li class="breadcrumb-item active">Create User group </a></li>
</ol>
<h1 class="page-title">Create user group</h1>
@endsection
@section('content')

            

            
               




            <form class="form-horizontal" role="form" method="POST" action="{{route('cp-user-group-store')}}">
                
                {{ csrf_field() }}

            

                @component('laradmin::components.input_text',['name'=>'name','value'=>'','required'=>'required'])
                @endcomponent 

                @component('laradmin::components.textarea',['name'=>'description','value'=>''])
                @endcomponent 
                
                
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        
                        <a class="btn btn-warning" href="{{route('cp-user-groups')}}">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Create
                        </button>
                    </div>
                </div>
        
            </form>
        
        

@endsection


