@extends('laradmin::cp.layouts.app')
@section('page-top')
    <h1 class='page-title'>{{$pageTitle??' '}}</h1>
@endsection

@section('content')
<div class="cp-plugins-view">       
    <div class="row">
        @foreach($plugins as $plugin)
        <div class="col-md-6 text-center">
            <div class="cp-plugins-view-item">
                <div class="icon-box ">
                    <img alt="{{$plugin['title']}}" src="{{$plugin['thumbnail_url']??'https://via.placeholder.com/150x150?text='.urlencode($plugin['title'])}}" > 
                </div>
                <h3><a href="{{route('cp-plugin',['tag'=>urlencode($plugin['tag'])])}}" style="text-decoration:none;">
                        
                        
                        {{$plugin['title']}} 
                        
                        
                    </a>
                </h3>
                <div class="notice">
                    @if($plugin['installed'])
                    
                        @if($plugin['installed']==1)
                            <span class="label label-success"><i class="fas fa-check"></i> Intalled</span>
                        @elseif($plugin['installed']==-1)
                            <span class="label label-default"><i class="fas fa-ban"></i> Disabled</span>
                        @endif
                        @if($plugin['updating']==1)
                            <span class="label label-warning"><i class="fas fa-info"></i> Updating</span>
                        @endif
            
                    @elseif($plugin['installed']==0)
                        {{---Do nothin--}}
                    @endif
                    
                    
                    @if($plugin['error_count'])
                    <span class="label label-danger"><i class="fas fa-exlamation-triangle"></i> {{$plugin['error_count']}} error(s)</span>
                    <div class="error-msg">
                        
                        <ul class="alert alert-danger list-unstyled">@foreach($plugin['error_msgs'] as $error_msg)<li>{{$error_msg}}</li> @endforeach</ul>
                    </div>
                    @endif
                </div>
                <div class="description">
                    @if(strlen($plugin['description'])>30)
                        {{substr($plugin['description'],0,140)}}<a href="{{route('cp-plugin',['tag'=>urlencode($plugin['tag'])])}}" class=" fainted-05"> <i class="fas fa-ellipsis-h"></i> <i class="fas fa-chevron-circle-right"></i></a>
                    @else
                        {{$plugin['description']}}
                    @endif
                </div>
                <a href="{{route('cp-plugin',['tag'=>urlencode($plugin['tag'])])}}" class="btn btn-primary btn-small">Review</a>
                
            </div>                     
        </div>
        @endforeach
        

    </div>   
            

</div>
                
 
@endsection
