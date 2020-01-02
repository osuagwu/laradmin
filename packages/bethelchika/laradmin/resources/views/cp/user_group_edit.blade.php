 
@extends('laradmin::cp.layouts.app')
@section('page-top')
<ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('cp')}}">Control panel</a></li>
                <li class="breadcrumb-item "><a href="{{route('cp-user-groups')}}">User groups</a></li>
                <li class="breadcrumb-item active">Edit user group </a></li>
</ol>
<h1 class="page-title">Edit user group</h1>
@endsection
@section('content')

            
            
             


                <form class="form-horizontal" role="form" method="POST" action="{{route('cp-user-group-update',$userGroup->id)}}">
                    {{ method_field('PUT') }}
                    {{ csrf_field() }}

                

                    @component('laradmin::components.input_text',['name'=>'name','value'=>$userGroup->name,'required'=>'required'])
                    @endcomponent 

                    @component('laradmin::components.textarea',['name'=>'description','value'=>$userGroup->description])
                    @endcomponent 
                    
                    
                    <div class="form-group">
                        <div class="col-md-6">
                            
                            <a class="btn btn-warning" href="{{route('cp-user-groups')}}">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Save
                            </button>
                        </div>
                    </div>
        
                </form>
            
        
        

@endsection


