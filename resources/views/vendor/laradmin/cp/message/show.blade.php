@extends('laradmin::cp.layouts.app')
@section('page-top')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('cp')}}">Control Panel</a></li>
    <li class="breadcrumb-item "> <a href="{{route('cp-user-message-index')}}">cMessage</a></li>
    <li class="breadcrumb-item active">{{$parentMessage->subject}}</li>
</ol>
<h1 class='page-title'>{{$pageTitle??$parentMessage->subject}}</h1>
@endsection
@section('content')

            
    <div class="message-container">
        
       <div class="panel-group" id="user-message-accordion" >
            
            @foreach($messages as $message)
                <div id="u-m-{{$message->id}}" class="panel user-message-item  
                                                        @if($user->is($message->sender)) {{' sent '}} @endif 
                                                        @if($user->is($message->user) ) 
                                                            @if($message->read_at ) {{' panel-default read '}} @else {{' panel-info unread '}} @endif 
                                                        @else {{' panel-default '}} @endif">
                    <div class="panel-heading">
                        <h4 class="panel-title ">
                            <a data-toggle="collapse" data-parent="#user-message-accordion" href="#u-m-collapse1-{{$message->id}}">
                                <span class="collapse-direction">
                                    <span class="glyphicon glyphicon-collapse-down"></span>
                                </span>
                                <span class="subject">
                                    {{$message->sender->name}} @if($message->adminSender) ({{$message->adminSender->name}}) @endif
                                    @if($loop->first)―{{$message->subject}}@endif
                                </span>
                                <small class="date-sent">
                                    <span class="glyphicon glyphicon-time"></span>
                                    {{$message->created_at->diffForHumans()}}
                                </small>
                            </a>
                        </h4>
                    </div>
                    <div id="u-m-collapse1-{{$message->id}}" class="panel-collapse collapse " data-message-id="{{$message->id}}">
                        <div class="panel-body">
                        {{-------------------------------------------------}} 
                            <div  class="user-message-content">
                                <div class="row ">
                                    <div class="col-xs-6 text-muted">
                                        <h4 class="text-info message-header">
                                            <span class="subject">{{$message->subject}}</span>
                                            <br /> 
                                            <small>
                                                <i class=" text-muted from">From</i>
                                                <b class="message-sender">{{$message->sender->name}} @if($message->adminSender) ({{$message->adminSender->name}}) @endif</b> 
                                                <i class="text-muted to">to </i>
                                                <b class="message-sender">{{$message->user->name}}</b> 
                                                <span class="channels text-muted"><i class="via">Via: </i><i class="channels">{{(str_ireplace('database','internal',implode(',',$parentMessage->channels)))}}</i></span>
                                            </small>
                                        </h4>
                                    </div>
                                    <div class="col-xs-6 text-muted text-right">
                                        <small class="date-sent"><span class="glyphicon glyphicon-time"></span>
                                        {{$message->created_at->diffForHumans()}}</small>
                                    </div>
                                    
                                </div>

                                <div>{{$message->message}}</div>
                                <div class="text-right text-muted ">
                                    @if($user->is($message->sender)==false or ($user->is($message->sender) and $message->user->is($message->sender)) )
                                    <small class="read_at label label-info">
                                        @if($message->read_at)
                                            <span class="glyphicon glyphicon-ok"></span> {{$message->read_at}}       
                                        @else
                                            <span class="glyphicon glyphicon-bell"></span>
                                        @endif
                                    </small> 
                                    @endif
                                    @if($message->parent_id or count($messages)==1){{--DOnt provide delete link for parent message unless it is the only message--}}
                                        <form title="Delete message" style="display:inline" method="post" action="{{route('cp-user-message-delete',$message->id)}}">
                                                    {{method_field('DELETE')}}
                                                    {{ csrf_field() }}
                                            
                                            
                                            
                                            <button type="submit" style="background-color:transparent;border:none;" class="glyphicon glyphicon-remove text-danger"></button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            {{------------------------------}}
                        
                        </div>
                    </div>
                </div>
                    
             
                  
            @endforeach
        </div>
        <div class="text-center">{{$messages->links()}}</div>
           
        
    </div><!--message-container-->
    @if(!$parentMessage->do_not_reply)
    <div class=""> 
        
        <form title="Rely form" class="form-horizontal" method="POST" action="{{route('cp-user-message-reply')}}">
            
            {{ csrf_field() }}
            {{--NOTE: for the email, it can either be the sender or the user(receiver) of the parent email and one of these should be equal to the Auth::user()->email --}}
            {{--<input type="hidden" name="email" value="@if(!strcmp($parentMessage->sender->email,Auth::user()->email)) {{$parentMessage->user->email}} @else {{$parentMessage->sender->email}} @endif" />--}}
            <input type="hidden" name="parent_id" value="{{$parentMessage->id}}"/> 
            <input type="hidden" name="subject" value="{{$parentMessage->subject}}" /> 
            <input type="hidden" name="channels" value="{{implode(',',$parentMessage->channels)}}" /> 
            @component('laradmin::blade_components.textarea',['name'=>'message','value'=>''])
            @endcomponent 
            <div class="form-group">
                <div class="col-md-6 col-md-offset-4">
                    
                    
                    <input type="submit" class="btn btn-primary" value="Reply" >
                    
                </div>
            </div>
            
            
        </form>
        <a class="btn btn-primary" href="{{route('cp-user-message-index')}}">Back to messages</a>
        
    </div>
    @endif

    @push('footer-scripts')
    <script>
        /*Message accordion callback. Also used to trigger marking messages as read
        */
        jQuery(document).ready(function(){
            $("#user-message-accordion").on("hidden.bs.collapse", function(evt){
                var target=$(evt.target);
                var messageId=target.data('message-id');
                var eleId='#u-m-'+messageId;
                $(eleId).find('.collapse-direction').html('<span class="glyphicon glyphicon-collapse-down"></span>');
                
                $(eleId).removeClass('active');
                //$(eleId).addClass('panel-default');
            });
            $("#user-message-accordion").on("shown.bs.collapse", function(evt){
                var target=$(evt.target);
                var messageId=target.data('message-id');
                var eleId='#u-m-'+messageId;
               $(eleId).find('.collapse-direction').html('<span class="glyphicon glyphicon-collapse-up"></span>');
               
               $(eleId).addClass('active');
               //$(eleId).removeClass('panel-default');

               //Mark message as read
               if($(eleId).hasClass('unread')){
                    var messageIds=[messageId];
                    //alert(messageIds)
                    markMessageAs(messageIds,'read');
               }
            });
        });

        /* Makes an ajax call to the server asking for messages to be marked as read/unread
        */
        function markMessageAs(messageIds,markAs){
            //var markAs='read';
            var url='{{route('cp-user-message-mark-ajax')}}';
            var token= '{{ csrf_token() }}';
            var jqxhr = $.post(url,{message_ids:messageIds,mark_as:markAs,'_token':token,'_method':'PUT'})
            .done(function(data) {
                $.each(data,function(nd){
                    var readAt=data[nd].read_at;
                    //console.log(readAt)
                    var eleId='#u-m-'+data[nd].id;
                    if(readAt==0){
                        $(eleId+' .read_at ').html('<span class="glyphicon glyphicon-bell"></span>');
                        
                        //$(eleId).removeClass('panel-warning');
                    }
                    else{
                        $(eleId+' .read_at ').html('<span class="glyphicon glyphicon-ok"></span> <span>'+readAt.date+' </span>');
                        
                        $(eleId).addClass('panel-default');
                        $(eleId).removeClass('panel-info');
                    }
                })
            })
            .fail(function(data) {
                
            })
            .always(function(data) {
                
            })
        }
    </script>
    @endpush          


  
@endsection