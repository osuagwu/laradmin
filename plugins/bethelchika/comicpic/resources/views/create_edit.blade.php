@extends('laradmin::user.layouts.app')
@include('comicpic::scripts')
@section('content')
<section class="section section-primary   section-title section-diffuse section-light-bg">
    <div class="container">
        <ol class="breadcrumb bg-transparent">
            <li class="breadcrumb-item"><a href="{{route('comicpic.index')}}">{{$appname}}</a></li>
            <li class="breadcrumb-item active">Create/edit</li>
        </ol>
        <h1 class="heading-1 content-title   "> Create {{$appname}} <small class="text-white"> - your very own creation!</small></h1>
        {{-- <nav><!--THIS IS NOT DISPLAYED B/C OF JUST TESTING S DIFFERENT LOOK-->
            <ul class="nav nav-tabs nav-justified nav-flat">
                @include('laradmin::menu',['tag'=>'primary.comicpic'])
            </ul>
        </nav> --}}
    </div>
</section>
<section class="section section-subtle section-diffuse section-light-bg section-diffuse-no-shadow ">     
    <div class="container">
        {{--  <div class="text-right">
            <p>
                <a href="{{route('comicpic.me')}}" class="btn btn-primary btn-xs">My {{$appname}}</a>
                <a href="{{route('comicpic.create')}}" class="btn btn-primary btn-xs">Upload</a>
                <br /><br />
            </p>
        </div>  --}}
        {{-- <div class="sidebar-mainbar"> --}}
            {{-- <aside class="sidebar">
                <a href="{{route('comicpic.me')}}" class="btn btn-info">My {{$appname}}</a>
                <a href="{{route('comicpic.create')}}" class="btn btn-primary">Upload</a>
            </aside> --}}
    
                <!-- Page Content Holder -->
            {{-- <div class="mainbar"> --}}
                    
        

        
        
        
        {{--FORM FOR ADDING DETAILS/EDITING AFTER FILE UPLOAD--}}
        @if(isset($media)) 
        <h1 class="heading-1 content-title">{{$pageTitle}}</h1>
        @include ('laradmin::inc.msg_board')
        <div class="row">
            <div class="col-md-6">
                <div class="sub-content with-padding no-elevation">
                    
                    <form class="form-horizontal" role="form" method="POST" action="{{route('comicpic.update')}}">     
                        {{ method_field('PUT') }}
                        {{ csrf_field() }}
                        <input type="hidden" name="comicpic_id" value="{{$comicpic->id}}" />  
                        @component('laradmin::components.input_text',['name'=>'title','value'=>$comicpic->title,'required'=>'required','placeholder'=>'Title'])
                        @endcomponent        
                        @component('laradmin::components.textarea',['name'=>'description','value'=>$comicpic->description,'required'=>'required','placeholder'=>'Description'])
                        @endcomponent 
                        @component('laradmin::components.input_text',['name'=>'hashtags','value'=>$comicpic->hashtags,'help'=>'e.g #fun,#love,#vote ','placeholder'=>'e.g #fun,#love,#vote','placeholder'=>'Comma separated hashtags'])
                        @endcomponent 
                        @component('laradmin::components.input_text',['name'=>'twitter_screen_names','value'=>$comicpic->twitter_screen_names,'help'=>'e.g bethelchika,obama','placeholder'=>'Comma separated screen names'])
                        @endcomponent 
                        @component('laradmin::components.input_text',['name'=>'twitter_via','value'=>$comicpic->twitter_via,'help'=>'BBCNEWS','placeholder'=>'Via name'])
                        @endcomponent 
                        {{--
                        @component('laradmin::components.input_text',['name'=>'tags','value'=>'','required'=>'required'])
                        @endcomponent
                        --}}
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4 text-center">
                                <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save "></i> Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="show-box show-box-edit sub-content no-elevation">
                    <div class="content-top">
                        
                        <form class="text-right"  role="form" method="POST" action="@if($comicpic->published_at){{route('comicpic.unpublish',$comicpic->id)}} @else {{route('comicpic.publish',$comicpic->id)}} @endif"> 
                                {{ method_field('PUT') }}
                                {{ csrf_field() }}
                            <button type="submit"  class="btn btn-primary btn-xs">
                                @if($comicpic->published_at) <span class="text-danger"><i class="fas fa-times"></i></span> Unpublish @else <i class="far fa-newspaper"></i> Publish @endif
                            </button>
                        </form>
                    </div>
                    <div class="text-center">
                        <img class="screenshot" src="{{Storage::disk('public')->url($media->getFullName())}}" alt="Uploaded file" /> 
                    </div>
                    <div class="content-bottom">
                        
                        <h4>{{$comicpic->title}} <br /><small>{{$comicpic->description}}</small></h4>
                    </div>
                </div>
            </div>
        </div>

        @else

        @include ('laradmin::inc.msg_board')
        {{--FORM FOR UPLOADING THE FILE--}}
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                {{--  <h1 class="heading-1 text-center extra-padding-bottom">{{$pageTitle}}</h1>  --}}

                @component('laradmin::components.form_dropzone',['action'=>route('comicpic.create'),'name'=>'file','help'=>'Upload an image such as jpg, png, gif, svg etc'])
                    @slot('script')
                        <script src="{{url('vendor/comicpic/assets/js/dropzone_manager.js')}}">
                        </script>
                    @endslot
                    @slot('form_content')
                        <div class="dz-message">
                            <span class="upload-icon">
                                <i class="fas fa-upload"></i>
                                
                                <span class="select-message">Click to select images</span>
                            </span>
                            <span>or drop image here to upload</span>
                        </div>
                    @endslot
                @endcomponent

                {{--<p class=" extra-padding-top extra-padding-bottom"><a href="{{route('comicpic.index')}}" class="btn btn-default btn-sm"> <span class="glyphicon glyphicon-chevron-left"></span> Back to home </a></p>--}}
            </div>
        </div>
        @endif

            
        
            {{-- </div>
        </div> --}}
    </div>    
</section>


@endsection
