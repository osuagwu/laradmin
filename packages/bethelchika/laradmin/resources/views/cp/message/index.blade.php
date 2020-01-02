@extends('laradmin::cp.layouts.app')
@section('page-top')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('cp')}}">Control panel</a></li>
    <li class="breadcrumb-item active"></span> cMessage</li>
</ol>
<h1 class='page-title'>{{$pageTitle??'cMessages'}}</h1>
@endsection
@section('content')





    <!-- list controls-->
    @component('laradmin::components.table_nav',[
                                                'tableName'=>'user_messages',
                                                'actions'=>[
                                                    'delete'=>['formAction'=>route('cp-user-message-deletes'),'label'=>'Delete selected messages'],
                                                ],
                                                'links'=>[
                                                    ['label'=>'Clear Search','url'=>URL::current()],
                                                    ['label'=>'New message','url'=>route('cp-user-message-create')],
                                                ]
                                            ])
    @endcomponent 
                
    <!-- start listing data-->       
    <div class="message-container table-responsive">
        
        <table class="table table-hover">
            <thead>
                <tr>
                <th >
                    @if(count($messages))
                        @component('laradmin::components.table_row_checkbox',['tableName'=>'user_messages'])
                        @endcomponent
                    @endif 
                </th>
                <th></th>
                <th>
                    @component('laradmin::components.sort_links',['orderBy'=>'subject','currentOrder'=>$currentOrder])
                    @endcomponent  
                </th>
                <th>
                    <span class="glyphicon glyphicon-time"></span>
                    @component('laradmin::components.sort_links',['orderBy'=>'created_at','currentOrder'=>$currentOrder])
                    @endcomponent  
                </th>

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
                        @component('laradmin::components.table_row_checkbox',['tableName'=>'user_messages','value'=>$converse['message']->id,'isHeadCheckbox'=>false])
                        @endcomponent
                    </td>
                    <td>
                        <span class="sender-name">
                            {{$converse['sender_name']}} @if($converse['message']->adminSender)({{$converse['message']->adminSender->name}}) @endif
                        </span>
                    </td>
                    

                    <td>
                        <a  href="{{route('cp-user-message-show',$converse['message']->id)}}" >
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
                        
                        @component('laradmin::components.table_row_delete',['formAction'=>route('cp-user-message-delete',$converse['message']->id)])
                        @endcomponent
                        
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        
    </div><!--message-container-->
    <div class="text-right"> {{$messages->links()}}</div>
               


  
@endsection