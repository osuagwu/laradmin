@extends('laradmin::cp.layouts.app')
@section('page-top')
    <h1 class='page-title'>{{$pageTitle??' '}}</h1>
@endsection

@section('content')                  
<div class="cp-plugin-show">
    <div class="icon-box">
        <img alt="{{$plugin['title']}}" src="{{$plugin['img_url']??'https://via.placeholder.com/450x250?text='.urlencode($plugin['title'])}}" >
    </div>
    
    <p class="description">{{$plugin['description']}}</p>
        
    
    <div class="row">
        <div class="col-sm-12">

            @if($plugin['installed'])
                
                
                @if($plugin['installed']==1)
                    
                    @if(!$plugin['error_count'])
                    <div class="jumbotron text-center">
                        <h3 id="cp-plugin-publish-redirect-wait">Please wait ...</h3>
                        <form id="cp-plugin-publishing-form-1"  class="text-right" method="post" action="{{route('cp-plugin-publish')}}" style="display:inline"> 
                                {{csrf_field()}} {{method_field('put')}}
                                <input type="hidden" name="tag" value="{{$plugin['tag']}}">    
                            <button id="cp-plugin-publishing-form-1-submit-btn" style="display:none;" type="submit" class="btn btn-warning btn-lg">Publish plugin</button>               
                        </form> 
                        @push('footer-scripts') 
                        <script >
                            $(function(){
                                $('#cp-plugin-publishing-form-1').submit()
                                $( "#cp-plugin-publish-redirect-wait" ).fadeOut( 5000, function() {
                                $( "#cp-plugin-publishing-form-1-submit-btn" ).fadeIn( 2000 );
                                });
                            })
                            
                        </script>
                        @endpush
                    </div> 
                    @endif
                @elseif($plugin['installed']==-1 )
                    <p class="alert alert-danger" >You cannot publish a disabled plugin</p>
                @endif

            @elseif($plugin['installed']==0 and !$plugin['error_count'])
                <p class="alert alert-danger" >Plugin is not installed</p>
            @endif
            
            <a href="{{route('cp-plugin',['tag'=>$plugin['tag']])}}" >Back to all plugin details</a>
        </div>
        {{-- <div class="col-sm-2 text-right">
                
        </div> --}}
    </div>
</div>    
        
        
          


                
 
@endsection
