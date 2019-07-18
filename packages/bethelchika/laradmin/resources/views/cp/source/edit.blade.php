 
@extends('laradmin::cp.layouts.app')

@section('page-top')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('cp')}}">Control panel</a></li>
    <li class="breadcrumb-item "><a href="{{route('cp-users')}}">Source</a></li>
    <li class="breadcrumb-item active">Edit a source</a></li>
</ol>
<h1 class="page-title">Edit Source</h1>
@endsection

@section('content')         
            
            

            @component('laradmin::blade_components.panel')
                @slot('title')
                    Edit source form
                @endslot 
               




                <form class="form-horizontal" role="form" method="POST" action="{{route('cp-source-edit',[$source->type,$source->id])}}">
                   
                    {{ csrf_field() }}
                    @method('put')
                    

                    @component('laradmin::form.components.input_text',['name'=>'name','value'=>$source->name,'required'=>'required'])
                    @endcomponent  
                    @component('laradmin::form.components.input_select',['name'=>'type','value'=>$source->type,'required'=>'required','options'=>$source_types])
                    @endcomponent                   

                    @component('laradmin::form.components.textarea',['name'=>'description','value'=>$source->description,'required'=>'required'])
                    @endcomponent  
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            
                            <a class="btn btn-warning" href="{{url()->previous()}}">
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


