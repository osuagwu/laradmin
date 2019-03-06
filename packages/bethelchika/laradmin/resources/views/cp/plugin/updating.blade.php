@extends('laradmin::cp.layouts.app')
@section('page-top')
    <h1 class='page-title'>{{$pageTitle??' '}}</h1>
@endsection

@section('content')                  
<div class="cp-plugin-show">
    <div class="icon-box">
        <span class="fas fa-plug fainted-09"> </span>
    </div>
    
    <p class="description">{{$plugin['description']}}</p>
        
    
    <div class="row">
        <div class="col-sm-12">
            @if($plugin['error_count'])
                <span class="label label-danger"><i class="fas fa-exlamation-triangle"></i> {{$plugin['error_count']}} error(s)</span>
                <div class="error-msg">
                    
                    <ul class="alert alert-danger list-unstyled">@foreach($plugin['error_msgs'] as $error_msg)<li>{{$error_msg}}</li> @endforeach</ul>
                </div>
            @endif

            @if($plugin['installed'])
                <div class="jumbotron text-center">
                    <form   class="text-right" method="post" action="{{route('cp-plugin-update')}}" style="display:inline"> 
                        {{csrf_field()}} {{method_field('delete')}}
                        <input type="hidden" name="tag" value="{{$plugin['tag']}}">    
                        <button   type="submit" class="btn btn-warning btn-lg">Cancel update</button>               
                    </form> 
                
                    @if(!$plugin['error_count'])
                    
                        
                        <form   class="text-right" method="post" action="{{route('cp-plugin-update')}}" style="display:inline"> 
                                {{csrf_field()}} {{method_field('put')}}
                                <input type="hidden" name="tag" value="{{$plugin['tag']}}">    
                            <button   type="submit" class="btn btn-primary btn-lg">Start update</button>               
                        </form> 
                        @push('footer-scripts') 
                        
                        @endpush 
                    
                    @endif
                </div>

            @elseif($plugin['installed']==0 and !$plugin['error_count'])
                <p class="alert alert-danger" >Plugin is not installed</p>
            @endif
            
            
        </div>
        {{-- <div class="col-sm-2 text-right">
                
        </div> --}}
    </div>
</div>    
        
        
          


                
 
@endsection
