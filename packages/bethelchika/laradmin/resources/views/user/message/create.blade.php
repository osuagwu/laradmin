@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.content_manager.stacks')
@section('content-top')


@endsection

@section('content')
<section class="section section-subtle  section-full-page ">
    <div class="container-fluid" >
        <div class="sidebar-mainbar">
                
            @component('laradmin::components.sidebar')
                @slot('content')
                <nav>
                    <ul class=" list-unstyled padding-top-x3">
                        <li role="presentation" class=""><a class="" href="{{route('user-message-index')}}"><span class="glyphicon glyphicon-chevron-left"></span> Back to messages</a></li>
                        <hr class="mid-rule padding-top-x4">
                        <li role="presentation" class="active"><a href="{{route('user-message-create')}}" ><i class="fas fa-plus-circle"></i> New message</a></li>
                        
                    </ul>  
                </nav> 
                @endslot 
            @endcomponent
        
                    <!-- Page Content Holder -->
            <div class="mainbar" role="main">
                <ol class="breadcrumb bg-transparent">
                    <li class="breadcrumb-item"><a href="{{route('user-profile')}}">me</a></li>
                    <li class="breadcrumb-item "><a href="{{route('user-message-index')}}"><i class="fas fa-envelope"></i>  uMessage</a></li>
                    <li class="breadcrumb-item active"> <i class="fas fa-plus-circle"></i>  New uMessage</li>
                </ol>
                <div class="row row-content-wrapper-default"> 
                    <div class="col-md-8  col-md-offset-2">
                        <div class="title-box text-center">
                                
                            
                            <h1 class='heading-1 content-title'>
                                @if($isSupport)
                                    Support message
                                @else
                                    {{$pageTitle??'New uMessage'}}
                                    @if($userTo) to {{$userTo->name}} 
                                    @endif
                                @endif

                            </h1>
                            
                            <div class="title-legend">
                                <span>Create and send a new message</span>
                            </div>
                        </div>
                        @include ('laradmin::inc.msg_board')
                        @include('laradmin::inc.email_confirmation_prompt')


                    

                        <div class="message-container">
                            

                                

                            <form class="form-horizontal" role="form" method="post" action="{{route('user-message-store')}}">
                                        
                                        {{ csrf_field() }}
                                @if($showChannels)
                                    @component('laradmin::components.input_select',['name'=>'channels','value'=>"database",'options'=>['email'=>'Email','email,database'=>'Email and internal','database'=>'Internal'],'required'=>'required'])
                                    @endcomponent
                                @endif

                                @if($userTo)
                                    <input type="hidden" name="user" value="{{$userTo->id}}" />
                                @elseif($isSupport)
                                    <input type="hidden" name="support" value="support" />
                                @else
                                    @component('laradmin::components.input_text',['name'=>'user','value'=>''])
                                    @endcomponent
                                    <div class="text-center"><h4>OR</h4></div>
                                    @component('laradmin::components.input_text',['name'=>'email','value'=>'','label'=>'Email (recipient\'s)'])
                                    @endcomponent
                                    <hr class="hr" />
                                @endif
                                
                                @component('laradmin::components.input_text',['name'=>'subject','value'=>'','required'=>'required'])
                                @endcomponent 

                                @component('laradmin::components.textarea',['name'=>'message','value'=>''])
                                @endcomponent 
                                
                                <input type="hidden" name="parent_id" value="{{$parent_id}}" />
                                <input type="hidden" name="return_to_url" value="{{old('return_to_url',$returnToUrl)}}" />

                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                    
                                        <a class="btn btn-subtle" href="{{route('user-message-index')}}">
                                            Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            Send
                                        </button>
                                    </div>
                                </div>
                            </form>
                                
                            
                            
                        </div><!--message-container-->
                        
                            


                        
                        
                    </div>
                </div>  
            </div>
        </div>
    </div>
</section> 
@endsection