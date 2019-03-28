@extends('layouts.app')
@include('comicpic::scripts')
@section('content')

 {{--Push OG metas--}}
 @include('comicpic::partials.item_metas')


<section class="section section-subtle  section-diffuse section-light-bg section-diffuse-no-shadow section-last"> 
    

   

    <div class="container">
        
        <div class="main-show @if($has_small_height){{'small-image'}}@endif">
            <div class="row">
                <div class="col-md-8">
                    <div class="show-box sub-content" >
                        {{--
                        <div class="content-top">
                                @component('laradmin::blade_components.user_avatar',['user'=>$comicpic->user,'legend'=>$comicpic->user->name,'sublegend'=>$comicpic->created_at])
                                @endcomponent
                        </div>
                        --}}
                        <div class="mainshot-box text-center">
                            <img class="mainshot" src="{{Storage::disk('public')->url($comicpic->medias[0]->getFullName())}}" alt="{{$comicpic->title}}" /> 
                            
                        </div>
                        <div class="content-bottom">
                            <h1 class="title">{{$comicpic->title}} </h1>
                            <div class="social-panel text-right">
                                <div class="social-panel-item text-muted">
                                    <div class="fb-like " data-href="{{url('/comicpic/show/'.$comicpic->id)}}" data-width="47" data-layout="standard" data-action="like" data-size="small" data-show-faces="true" data-share="true"></div>
                                </div>
                                <div class="social-panel-item text-muted">
                                    {{-- Twitte button start--}}
                                    <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-text="Check this out" data-url="{{url('/comicpic/show/'.$comicpic->id)}}" data-via="webferendum" data-hashtags="comicpic,webferendum" data-related="weferendum" data-show-count="true">Tweet</a>
                                    {{--Twitter script should be at page footer --}}
                                </div>
                                
                                
                                @if(!Auth::guest() and $comicpic->user_id==Auth::user()->id)
                                
                                <a class="social-panel-item" title="Edit" href="{{route('comicpic.edit',$comicpic->id)}}"><i class="fa fa-edit text-muted"></i></a>
                                <form  method="POST" action="{{route('comicpic.delete',$comicpic->id)}}">
                                    {{method_field('DELETE')}}
                                    {{csrf_field()}}
                                    <button class="social-panel-item btn-unstyled text-muted" title="Delete" ><i class="fa fa-times"></i></button>
                                </form>
                                
                                @endif
                            </div>
                            <div>
                                @component('laradmin::blade_components.user_avatar',['user'=>$comicpic->user,'legend'=>$comicpic->user->name,'sublegend'=>$comicpic->published_at,'class'=>'avatar-danger'])
                                @endcomponent
                            </div>
                            <div class="description">{{$comicpic->description}}</div>
                            <div class="fb-comments" data-href="{{url('/comicpic/show/'.$comicpic->id)}}" data-numposts="5" data-width="100%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-right">
                        <p>   
                            <a href="{{route('comicpic.me')}}" class="btn btn-primary btn-xs">My Comicpic</a>
                            <a href="{{route('comicpic.create')}}" class="btn btn-primary btn-xs">Upload</a>
                        </p>
                        {{--  <hr style="margin:0;" />  --}}
                        
                    </div>
                    <div class="related-list">
                        <h4 class="list-title">Related</h4>
                        @foreach($comicpics as $comicpic)
                            @if(!Storage::disk('public')->exists($comicpic->medias[0]->getFullName()))
                                @continue;{{--check that the file exists otherwise do not show--}}
                            @endif  
                            <div class="list-item"> 
                                <div class="row no-gutters">
                                    <div class="col-xs-4">
                                        
                                            <div class="screenshot-box">
                                                <a class="screenshot-link" href="{{route('comicpic.show',$comicpic->id)}}">                                        
                                                    <img class="screenshot" src="{{Storage::disk('public')->url($comicpic->medias[0]->getThumbFullName())}}" alt="{{$comicpic->title}}" /> 
                                                </a>
                                            </div>
                                            <div class="content-bottom">
                                                                    
                                            </div>
                                        
                                    </div>
                                    <div class="col-xs-8">
                                            <a href="{{route('comicpic.show',$comicpic->id)}}" class="title"><span >{{$comicpic->title}}</span></a>
                                            <div class="">
                                                @component('laradmin::blade_components.user_avatar',['user'=>$comicpic->user,'legend'=>$comicpic->user->name,'class'=>'avatar-info'])
                                                @endcomponent
                                            </div>
                                            <div>
                                                <span class="created-at"><i class="fa fa-calendar"></i> {{$comicpic->published_at}}</span>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>    
</section>


@endsection
