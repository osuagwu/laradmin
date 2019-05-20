
@extends('laradmin::cp.layouts.app')

@section('page-top')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('cp')}}">Control panel</a></li>
    <li class="breadcrumb-item "><a href="{{route('cp-users')}}">Users</a></li>
    <li class="breadcrumb-item active">Edit user </a></li>
</ol>
<h1 class="page-title">Edit user</h1>
@endsection

@section('content')


            @component('laradmin::blade_components.panel')
                @slot('title')
                    Edit profile
                @endslot 
               




                <form class="form-horizontal" role="form" method="POST" action="{{route('cp-user-update',$user->id)}}">
                    {{ method_field('PUT') }}
                    {{ csrf_field() }}

                    

                   

                    @component('laradmin::blade_components.input_text',['name'=>'name','value'=>$user->name,'label'=>'Name (Screen name)','required'=>'required'])
                    @endcomponent 

                    @component('laradmin::blade_components.input_text',['name'=>'first_names','value'=>$user->first_names,'label'=>'First names'])
                    @endcomponent 

                    @component('laradmin::blade_components.input_text',['name'=>'last_name','value'=>$user->last_name,'label'=>'Last name'])
                    @endcomponent

                    @component('laradmin::blade_components.input_text',['name'=>'year_of_birth','value'=>$user->year_of_birth,'label'=>'Year of birth'])
                    @endcomponent

                    @component('laradmin::blade_components.input_select',['name'=>'gender','value'=>$user->gender,'options'=>['female'=>'Female','male'=>'Male'],])
                    @endcomponent

                    @component('laradmin::blade_components.input_select',['name'=>'country','value'=>$user->country,'options'=>$countries,])
                    @endcomponent

                    @component('laradmin::blade_components.input_select',['name'=>'faith','value'=>$user->faith,'options'=>$faiths,])
                    @endcomponent
                    
                    <hr class="hr">
                    @component('laradmin::blade_components.input_password',['name'=>'new_password','label'=>'New password'])
                    @endcomponent 

                    @component('laradmin::blade_components.input_password',['name'=>'new_password_confirmation','label'=>'New password confirmation'])
                    @endcomponent 
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            
                            <a class="btn btn-warning" href="{{route('cp-user',$user->id)}}">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Update
                            </button>
                        </div>
                    </div>
            
                </form>
            @endcomponent
        

@endsection


