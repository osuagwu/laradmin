 
@extends('laradmin::cp.layouts.app')

@section('page-top')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('cp')}}">Control panel</a></li>
    <li class="breadcrumb-item "><a href="{{route('cp-users')}}">Source</a></li>
    <li class="breadcrumb-item active">Link a source</a></li>
</ol>
<h1 class="page-title">Link Source</h1>
@endsection

@section('content')         
            
            

            @component('laradmin::components.panel')
                @slot('title')
                    Link source form
                @endslot 
               




                <form class="form-horizontal" role="form" method="POST" action="{{route('cp-source-create')}}">
                   
                    {{ csrf_field() }}

                    

                    @component('laradmin::form.components.input_text',['name'=>'name','value'=>'','required'=>'required'])
                    @endcomponent  
                    @component('laradmin::form.components.input_select',['name'=>'type','value'=>'','required'=>'required','options'=>$source_types])
                    @endcomponent                   

                    @component('laradmin::form.components.textarea',['name'=>'description','value'=>'','required'=>'required'])
                    @endcomponent  
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            
                            <a class="btn btn-warning" href="{{url()->previous()}}">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Link
                            </button>
                        </div>
                    </div>
            
                </form>
            @endcomponent
        
@endsection


