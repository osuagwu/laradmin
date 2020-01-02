@extends('laradmin::cp.layouts.app')

@section('content')

    
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                @component('laradmin::components.panel')
                @slot('title')
                    Notifications
                @endslot
                
                <div class="notice-container">
                    @unless(count($notices))
                        <span>No notification.</span>
                    @endunless
                    @foreach($notices as $notice)
                        <div id="{{$notice->id}}" class="well well-sm">
                            <div class="row ">
                                <div class="col-xs-6 text-muted">
                                    <small class="text-info">
                                        <span title="Notice" class="glyphicon glyphicon-bell"> <i>{{$notice->data['name']}}</i>
                                        
                                    </small>
                                </div>
                                <div class="col-xs-6 text-muted text-right">
                                    <small class="text-danger"><small class="glyphicon glyphicon-time"></small>
                                    {{$notice->created_at->diffForHumans()}}</small>
                                </div>
                                
                            </div>

                            <div>{{$notice->data['notice']}}</div>
                            <div class="text-right text-muted ">
                                <small class="read_at label label-info">
                                    @if($notice->read_at)
                                                <span class="glyphicon glyphicon-ok"></span> {{$notice->read_at}}       
                                    @else
                                        <span class="glyphicon glyphicon-bell"></span>
                                    @endif
                                </small> 

                                <form title="Delete notification" style="display:inline" method="post" action="{{route('cp-notification-delete',$notice->id)}}">
                                            {{method_field('DELETE')}}
                                            {{ csrf_field() }}
                                            <button type="submit" style="background-color:transparent;border:none;" class="glyphicon glyphicon-remove text-danger"></button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                    <!--Now create js for marking notice as read/unread-->
                        @push('footer-scripts')
                            <script>
                            jQuery(document).ready(function(){
                                    notices2markAsRead=[];
                                    @foreach($notices as $notice)
                                        @if(!$notice->read_at)
                                            notices2markAsRead.push('{{$notice->id}}');
                                        @endif
                                    @endforeach
                                    if( notices2markAsRead.length){
                                        marknoticesAsRead(notices2markAsRead);
                                    }
                                
                            });
                            //mark notices as read
                            function marknoticesAsRead(notices){
                                var noti=notices;
                                
                                var url='{{route('cp-notification-mark-ajax')}}';
                                var token= '{{ csrf_token() }}';
                                var jqxhr = $.post(url,{notices:noti,mark_as:'read','_token':token})
                                .done(function(data) {
                                    $.each(data,function(nd){
                                        var readAt=data[nd].read_at;
                                        if(readAt==0){
                                            $('#'+data[nd].id+' .read_at ').html('<span class="glyphicon glyphicon-bell"></span>');
                                        }
                                        else{
                                            $('#'+data[nd].id+' .read_at ').html('<span class="glyphicon glyphicon-ok"></span> <span>'+readAt.date+' </span>');
                                        }
                                    })
                                })
                                .fail(function(data) {
                                
                                })
                                .always(function(data) {
                                    
                                })
                            }
                            </script>
                        @endpush()
                </div><!--notice-container-->
                <div class="text-right"> {{$notices->links()}}</div>
            @endcomponent   

            </div>
        </div>
    

@endsection