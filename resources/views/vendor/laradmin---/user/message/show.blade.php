@extends('laradmin::user.layouts.app')

@section('content')
<section class="section section-subtle section-first section-full-page section-last">
    <div class="container-fluid" >
        <div class="sidebar-mainbar">
                
            @component('laradmin::blade_components.sidebar')
                @slot('content')
                <nav>
                    <ul class="padding-top-x3 list-unstyled">
                        <li role="presentation" ><a class="" href="{{route('user-message-index')}}"><span class="glyphicon glyphicon-chevron-left"></span> Back to messages</a></li>
                        <hr class="midrule  padding-top-x4">
                        <li role="presentation" class=""><a href="{{route('user-message-create')}}" class=""><i class="fas fa-plus-circle"></i> New message</a></li>
                    </ul>
                </nav>

                @endslot 
            @endcomponent
        
                    <!-- Page Content Holder -->
            <div class="mainbar" role="main">
                <div class="title-box">
                        <ol class="breadcrumb bg-transparent">
                            <li class="breadcrumb-item"><a href="{{route('user-profile')}}">me</a></li>
                            <li class="breadcrumb-item "><a href="{{route('user-message-index')}}"> <i class="fas fa-envelope"></i>  uMessage</a></li>
                            <li class="breadcrumb-item active hidden-sm-down" title="{{$parentMessage->subject}}"> <i class="fas fa-envelope-open"></i> {{str_limit($parentMessage->subject,6,'...')}}</li>
                        </ol>
                    
                    <h1 class='heading-4 content-title'>{{$parentMessage->subject}}</h1>
                    
                    
                </div>
                <div class="row row row-content row-bg-content-margin bg-white content-padding-top content-padding-bottom">
                    <div class="col-md-12">
                        <div class="content content-default">
                            <div class="content-heading" >
                                    
                                    
                            </div>
                            
                            <div class="content-body" >
                                @include ('laradmin::inc.msg_board')
                                @include('laradmin::inc.email_confirmation_prompt')

                        
                                    
                                <div class="message-container">
                                    
                                <div class="panel-group" id="user-message-accordion" >
                                        
                                        @foreach($messages as $message)
                                            <div id="u-m-{{$message->id}}" class="panel user-message-item   
                                                                                @if(Auth::user()->is($message->sender)) {{' sent '}} @endif 
                                                                                @if(Auth::user()->is($message->user) ) 
                                                                                    @if($message->read_at ) {{' panel-default read '}} @else {{' panel-info unread '}} @endif 
                                                                                @else {{' panel-default '}} @endif">
                                                                                    
                                                <div class="panel-heading">
                                                    <h4 class="panel-title ">
                                                        <a data-toggle="collapse" data-parent="#user-message-accordion" href="#u-m-collapse1-{{$message->id}}">
                                                            <span class="collapse-direction">
                                                                <span class="glyphicon glyphicon-menu-down"></span>
                                                            </span>
                                                            <span class="participant">
                                                                {{$message->sender->name}}
                                                                {{--@if($loop->first)―{{$message->subject}}@endif--}}
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
                                                                            <b class="message-sender">{{$message->sender->name}}</b> 
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

                                                            <div class="message-body">{{$message->message}}</div>
                                                            <div class="message-footer text-right text-muted ">
                                                                    @if(Auth::user()->is($message->sender)==false or (Auth::user()->is($message->sender) and $message->user->is($message->sender)) )
                                                                <small class="read_at label label-info">
                                                                    @if($message->read_at)
                                                                        <span class="glyphicon glyphicon-ok"></span> {{$message->read_at}}       
                                                                    @else
                                                                        <span class="glyphicon glyphicon-bell"></span>
                                                                    @endif
                                                                </small> 
                                                                @endif
                                                                @if($message->parent_id or count($messages)==1){{--DOnt provide delete link for parent message unless it is the only message--}}
                                                                    <form class="delete-item" title="Delete message" style="display:inline" method="post" action="{{route('user-message-delete',$message->id)}}">
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
                                    
                                    <form  class="form-horizontal message-reply" title="Rely form" method="POST" action="{{route('user-message-reply')}}">
                                        
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
                                                
                                                
                                                <button type="submit" class="btn btn-primary btn-sm"  >
                                                        <i class="fas fa-reply"></i> Reply
                                                </button>
                                                
                                            </div>
                                        </div>
                                        
                                        
                                    </form>
                                    <a class="btn btn-default btn-xs" href="{{route('user-message-index')}}"><span class="glyphicon glyphicon-chevron-left"></span> Back to messages</a>
                                    
                                </div>
                                @endif

                            </div><!--.content-body-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

                @push('footer-scripts')
                <script>
                    /*Message accordion callback. Also used to trigger marking messages as read
                    */
                    jQuery(document).ready(function(){
                        $("#user-message-accordion").on("hidden.bs.collapse", function(evt){
                            var target=$(evt.target);
                            var messageId=target.data('message-id');
                            var eleId='#u-m-'+messageId;
                            $(eleId).find('.collapse-direction').html('<span class="glyphicon glyphicon-menu-down"></span>');
                            
                            $(eleId).removeClass('active');
                            //$(eleId).addClass('panel-default');
                        });
                        $("#user-message-accordion").on("shown.bs.collapse", function(evt){
                            var target=$(evt.target);
                            var messageId=target.data('message-id');
                            var eleId='#u-m-'+messageId;
                        $(eleId).find('.collapse-direction').html('<span class="glyphicon glyphicon-menu-up"></span>');
                        
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
                        var url='{{route('user-message-mark-ajax')}}';
                        var token= '{{ csrf_token() }}';
                        var jqxhr = $.post(url,{message_ids:messageIds,mark_as:markAs,'_token':token,'_method':'PUT'})
                        .done(function(data) {
                            $.each(data,function(nd){
                                var readAt=data[nd].read_at;
                                //console.log(readAt)
                                var eleId='#u-m-'+data[nd].id;
                                if(readAt==0){
                                    $(eleId+' .read_at ').html('<span class="glyphicon glyphicon-bell"></span>');
                                    //$(eleId).addClass('panel-info');
                                    //$(eleId).removeClass('panel-default');
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