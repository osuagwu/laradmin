@extends('layouts.app')
@include('comicpic::scripts')
@section('content')
<section class="section section-primary section-first  section-title">
    <div class="container">
        <ol class="breadcrumb bg-transparent">
            <li class="breadcrumb-item active">Comicpic</li>
        </ol>
        <h1 class="heading-3 content-title   ">Welcome to ComicPic</h1>
        <nav>
            <ul class="nav nav-tabs nav-flat">
                @include('laradmin::menu',['tag'=>'primary.comicpic'])
            </ul>
        </nav>
    </div>
</section> 
<section class="section section-subtle section-first section-last section-full-height section-extra-padding-bottom">     
    <div class="container">
        
        {{-- <div class="text-right">
            <p>
                <a href="{{route('comicpic.me')}}" class="btn btn-info btn-xs">My Comicpic</a>
                <a href="{{route('comicpic.create')}}" class="btn btn-primary btn-xs">Upload</a>
                <br /><br />
            </p>
        </div> --}}
        
        
        @include ('laradmin::inc.msg_board')
        {{$comicpics->links()}}
        @unless(count($comicpics))
        <p class="text-center alert alert-warning"><i class="fas fa-battery-empty"> </i> No item to display</p>
        @endif
        <div class="row first-content-padding">
            @foreach($comicpics as $comicpic)
                @if(!Storage::disk('public')->exists($comicpic->medias[0]->getFullName()))
                    @continue;{{--check that the file exists otherwise do not show--}}
                @endif
                
            <div class="col-xs-6 col-md-3 col-lg-2">
                <div class="view-box-sm">
                    
                    <div class="screenshot-box">
                        <a class="screenshot-link" href="{{route('comicpic.show',$comicpic->id)}}">
                            <div class="overlay">
                                <div class="overlay-content">
                                    <h4>{{substr($comicpic->title,0,35)}} <br /><small class="text-white">...</small></h4>
                            
                                </div>
                            </div>
                        
                            <img class="screenshot" src="{{Storage::disk('public')->url($comicpic->medias[0]->getThumbFullName())}}" alt="{{$comicpic->title}}" /> 
                        </a>
                    </div>
                    <div class="content-bottom">
                        <div class="">
                            @component('laradmin::blade_components.user_avatar',['user'=>$comicpic->user,'legend'=>$comicpic->user->name, 'class'=>'avatar-primary'])
                            @endcomponent
                        </div>
                        
                        <div class="social-panel social-panel-sm">
                            <a href="#" class="btn btn-primary btn-xs" tabindex="0" title="Share" role="button" data-html="true" data-toggle="popover" data-placement="auto bottom" data-content="<iframe scrolling='no'  src='{{route('comicpic.og',$comicpic->id)}}'></iframe>" data-trigger="click"><i class="fas fa-share"></i>
                                
                                 Share</a>
                            @if(!Auth::guest() and $comicpic->user_id==Auth::user()->id)
                            
                            <a class="social-panel-item" title="Edit" href="{{route('comicpic.edit',$comicpic->id)}}"><i class="fa fa-edit text-muted"></i></a>
                            <form  method="POST" action="{{route('comicpic.delete',$comicpic->id)}}">
                                {{method_field('DELETE')}}
                                {{csrf_field()}}
                                <button class="social-panel-item btn-unstyled text-muted" title="Delete" ><i class="fa fa-times"></i></button>
                            </form>
                            
                            @endif
                        </div>
                        
                    </div>
                </div>
            </div>
            @endforeach
        </div>
         {{$comicpics->links()}}
    </div>    
</section>
@endsection