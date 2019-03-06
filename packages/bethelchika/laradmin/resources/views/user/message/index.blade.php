@extends('laradmin::user.layouts.app')

@section('content')
<section class="section section-subtle section-first section-full-page section-last">
    <div class="container-fluid" >
        <div class="sidebar-mainbar">
                
            @component('laradmin::blade_components.sidebar')
                @slot('content')
                    
                    <ul class="nav nav-pills padding-top-x3">
                        <li role="presentation" class=""><a href="{{route('user-message-create')}}" ><i class="fas fa-plus-circle"></i> New message</a></li>
                        
                    </ul>
                    
                @endslot 
            @endcomponent
        
                    <!-- Page Content Holder -->
            <div class="mainbar">
                <div class="title-box">
                    <ol class="breadcrumb bg-transparent">
                        <li class="breadcrumb-item"><a href="{{route('user-profile')}}">me</a></li>
                        <li class="breadcrumb-item active"><i class="fas fa-envelope"></i> uMessage</li>
                    </ol>
                    
                    <h1 class='heading-1 content-title'>{{$pageTitle??'uMessage'}}</h1>
                    <div class="title-legend">
                        <span>Read and send messages</span>
                    </div>
                </div>
                <div class="row row-content-wrapper-default ">
                    <div class="col-md-12">
                        
                
                
                
                        @include ('laradmin::inc.msg_board')
                        @include('laradmin::inc.email_confirmation_prompt')



                        <!-- list controls-->
                        @component('laradmin::blade_components.table_nav',[
                                                                    'tableName'=>'user_messages',
                                                                    'actions'=>[
                                                                        'delete'=>['formAction'=>route('user-message-deletes'),'label'=>'Delete selected messages'],
                                                                    ],
                                                                    'links'=>[
                                                                        ['label'=>'New message','url'=>route('user-message-create'),'icon_class'=>'fas fa-plus-circle'],
                                                                    ],
                                                                    'selectAllCheckbox'=>false,

                                                                ])
                        @endcomponent 
                                    
                        <!-- start listing data-->       
                        <div class="message-container table-responsive">
                            
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th >
                                                @component('laradmin::blade_components.table_row_checkbox',['tableName'=>'user_messages','isHeadCheckbox'=>true])
                                                @endcomponent
                                        </th>
                                        
                                        
                                        <th></th>
                                        <th>
                                            @component('laradmin::blade_components.sort_links',['orderBy'=>'subject','currentOrder'=>$currentOrder])
                                            @endcomponent  
                                        </th>
                                        <th>
                                            <span class="glyphicon glyphicon-time"></span>
                                            @component('laradmin::blade_components.sort_links',['orderBy'=>'created_at','currentOrder'=>$currentOrder])
                                            @endcomponent  
                                        </th>
                                        <th></th>

                                    </tr>
                                </thead>
                                <tbody>
                                @unless(count($messages))
                                    <tr>
                                        <td><span>No message.</span></td>
                                    </tr>
                                @endunless
                                @foreach($converses as $converse)
                                    <tr class="message @if($converse['unread_count']) unread @else read @endif ">
                                        <td>
                                            @component('laradmin::blade_components.table_row_checkbox',['tableName'=>'user_messages','value'=>$converse['message']->id,'isHeadCheckbox'=>false])
                                            @endcomponent
                                        </td>
                                        <td>
                                            <span class="sender-name">
                                                {{$converse['sender_name']}} 
                                            </span>
                                        </td>
                                        

                                        <td>
                                            <a  href="{{route('user-message-show',$converse['message']->id)}}" >
                                            <span class="subject">
                                                    {{$converse['message']->subject}} 
                                            </span>
                                                @if($converse['unread_count'])
                                                    <small  class="badge">{{$converse['unread_count']}}</small>
                                                @endif 
                                            </a>
                                        </td>
                                        
                                        <td>
                                            {{--<small class="date-sent">{{$converse['message']->created_at->diffForHumans()}}</small> Open this to print the time in the listints--}}
                                        </td>
                                        <td>
                                            
                                            @component('laradmin::blade_components.table_row_delete',['formAction'=>route('user-message-delete',$converse['message']->id)])
                                            @endcomponent
                                            
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            
                        </div><!--message-container-->
                        <div class="text-right"> {{$messages->links()}}</div> 
                    </div>
                </div>  
            </div>
        </div>
    </div> 
</section>


@endsection