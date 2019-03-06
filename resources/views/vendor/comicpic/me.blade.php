@extends('layouts.app')
@include('comicpic::scripts')
@section('content')
<section class="section section-danger section-first  section-title">
    <div class="container">
        <ol class="breadcrumb bg-transparent">
            <li class="breadcrumb-item"><a href="{{route('comicpic.index')}}">Comicpic</a></li>
            <li class="breadcrumb-item active">me</li>
        </ol>

        <!----------------------------------------->
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-xs-3">
                        @component('laradmin::blade_components.user_icon',['user'=>Auth::user(),'size'=>'lg'])
                        @endcomponent
                    </div>
                    <div class="col-xs-9">
                            <h2 class="heading-1 padding-top-x9  content-title "> {{Auth::user()->name}}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h1 class="heading-big content-title text-right  fainted-07  "> My Comic Pics <small class="text-white"> - your very own creation!</small></h1>
            </div>
        </div>
        <!---------------------------------------------------->

        <nav>
            <ul class="nav nav-tabs nav-justified nav-flat">
                @include('laradmin::menu',['tag'=>'primary.comicpic'])
            </ul>
        </nav>
    </div>
</section>
<section class="section section-subtle section-last first-content-padding section-extra-padding-bottom">     
{{--Example of minimalistic UI (Miniui,miniUI)
    ----------------------------------------------------------
    <miniui min-height="100px" min-width="200px" responsive="true">
        <require>
            <mandatory>
                <project name="jquery" version="^3.1.1"" />
                <script src="http://vendor/laradmin/js/gen.js"></script>
                <link href="http://css/app-structure.css'" rel="stylesheet" />
                
            </mandatory>
            <link href="http://css/app-colors.css'" rel="stylesheet">
        </require>
        <menu>
            <a href="#home">Home</a>
            <a href="#about">About</a>
        </menu>
        <in>
            <item name="text_to_edit">
                Description of text_to_edited goes here
            </item>
        </in>
        <out>
            <item name="edited_text">
                Description of edited_text goes here
            </item>
        </out>
        <comments>
            # Summary
            ------
            ## Description
            ------

            ## In and Out
            ### IN
            Description of text_to_edited goes here

            ### OUT
            Description of edited_text goes here
        </comments>    
    </miniui>
    ---------------------------------------------------------
    The <section> should be used and  thus: <section class="miniui" data-ui-type="miniui">...</section>
--}}

    <div class="container">
        
       
        
        @include ('laradmin::inc.msg_board')
        {{$comicpics->links()}}
        @unless(count($comicpics))
        <p class="text-center alert alert-warning"><i class="fas fa-battery-empty"> </i> You have no item to display</p>
        @endif
        <div class="row first-content-padding">
            @foreach($comicpics as $comicpic)
                
                
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
                            @component('laradmin::blade_components.user_avatar',['user'=>$comicpic->user,'legend'=>$comicpic->user->name,'class'=>'avatar-primary'])
                            @endcomponent
                        </div>
                        
                        <div class="social-panel social-panel-sm">

                            <a href="#" class="" tabindex="0" title="Share" role="button" data-html="true" data-toggle="popover" data-placement="auto bottom" data-content="<iframe scrolling='no'  src='{{route('comicpic.og',$comicpic->id)}}'></iframe>" data-trigger="click"><i class="fas fa-share"></i></a>
                                
                                
                            
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