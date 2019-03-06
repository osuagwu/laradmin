@extends('laradmin::cp.layouts.app')
@section('page-top')
    <h1 class='page-title'>{{$pageTitle??' '}}</h1>
@endsection

@section('content')                  
<div class="cp-plugin-show">
    <div class="icon-box">
        <span class="fas fa-plug fainted-09"> </span>
    </div>
        
      
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
    
    <p class="description">{{$plugin['description']}}</p>
        
    
    <div class="row">
        <div class="col-sm-12">

            @if($plugin['installed'])
                
                <form class="text-right" method="post" action="{{route('cp-plugin')}}" style="display:inline"> 
                    {{csrf_field()}} {{method_field('delete')}}
                    <input type="hidden" name="tag" value="{{$plugin['tag']}}"> 
                    <button type="submit"  class="btn btn-primary btn-small">Uninstall</button>               
                </form>
                @if($plugin['installed']==1)
                    <form class="text-right" method="post" action="{{route('cp-plugin-disable')}}" style="display:inline"> 
                            {{csrf_field()}} {{method_field('put')}}
                            <input type="hidden" name="tag" value="{{$plugin['tag']}}">     
                        <button type="submit"  class="btn btn-primary btn-small">Disable</button>               
                    </form> 
                    @if(!$plugin['error_count'])
                    <form class="text-right" method="post" action="{{route('cp-plugin-publish')}}" style="display:inline"> 
                            {{csrf_field()}} {{method_field('put')}}
                            <input type="hidden" name="tag" value="{{$plugin['tag']}}">    
                        <button type="submit"  class="btn btn-primary btn-small">Re-publish</button>               
                    </form>   
                    @endif
                @elseif($plugin['installed']==-1 and !$plugin['error_count'])
                    <form class="text-right" method="post" action="{{route('cp-plugin-enable')}}" style="display:inline"> 
                            {{csrf_field()}} {{method_field('put')}}   
                            <input type="hidden" name="tag" value="{{$plugin['tag']}}">  
                        <button type="submit"  class="btn btn-primary btn-small">Enable</button>               
                    </form>  
                @endif

            @elseif($plugin['installed']==0 and !$plugin['error_count'])
                <form method="post" action="{{route('cp-plugin')}}" style="display:inline" > 
                        {{csrf_field()}}  
                        <input type="hidden" name="tag" value="{{$plugin['tag']}}">   
                    <button type="submit"  class="btn btn-primary btn-small">Install</button>               
                </form> 
            @endif
            <a href="{{route('cp-plugin-update',['tag'=>$plugin['tag']])}}"  class="btn btn-{{$plugin['updating']?'warning':'primary'}} btn-small" >{{$plugin['updating']? 'Continue update':'Update'}}</a>
            <a href="{{route('cp-plugins')}}" >Back to all plugins</a>
        </div>
        {{-- <div class="col-sm-2 text-right">
                
        </div> --}}
    </div>
</div>    
        
        
          


                
 
@endsection
