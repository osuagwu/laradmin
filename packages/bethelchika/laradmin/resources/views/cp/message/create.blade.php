@extends('laradmin::cp.layouts.app')
@section('page-top')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('cp')}}">Control Panel</a></li>
    <li class="breadcrumb-item "> <a href="{{route('cp-user-message-index')}}">cMessage</a></li>
    <li class="breadcrumb-item active"></span> New cMessage</li>
</ol>
<h1 class='page-title'>{{$pageTitle??'New cMessage'}}@if($userTo) to {{$userTo->name}} @endif</h1>
@endsection

@section('content')


            

                <div class="message-container">
                    

                        

                    <form class="form-horizontal" role="form" method="post" action="{{route('cp-user-message-store')}}">
                                
                                {{ csrf_field() }}
                        @if($showChannels)
                            @component('laradmin::components.input_select',['name'=>'channels','value'=>"database",'options'=>['email'=>'Email','email,database'=>'Email and internal','database'=>'Internal'],'required'=>'required'])
                            @endcomponent
                        @endif

                        @if($userTo)
                            <input type="hidden" name="user" value="{{$userTo->id}}" />
                        @else
                            @component('laradmin::components.input_text',['name'=>'email','value'=>''])
                            @endcomponent
                        @endif

                        @component('laradmin::components.input_text',['name'=>'subject','value'=>'','required'=>'required'])
                        @endcomponent 

                        @component('laradmin::components.textarea',['name'=>'message','value'=>''])
                        @endcomponent 
                        
                        <input type="hidden" name="parent_id" value="{{$parent_id}}" />
                        <input type="hidden" name="return_to_url" value="{{old('return_to_url',$returnToUrl)}}" />

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                            
                                <a class="btn btn-warning" href="{{route('cp-user-message-index')}}">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Send
                                </button>
                            </div>
                        </div>
                    </form>
                        
                    
                    
                </div><!--message-container-->
                
              


 
@endsection