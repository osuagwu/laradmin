@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.content_manager.stacks')




@section('content')

<section role="banner" class="section section-default  section-title "  >
        <div class="section-overlay">
            <div class="container">
                <h1 class="heading-1 text-center">{{$pageTitle}}</h1>
                <a class="strong" href="{{route('user-profile')}}">&leftarrow; Done</a>
                <div class="">
                    <div class=" ">
                        
                        <div class="text-center">
                            <div class="">
                                
                            </div>
                            <div class="">
                                
                                <laradmin-image-upload-single
                                    source-url="{{route('user-cphoto-json')}}"
                                    upload-url="{{route('user-cphoto-json')}}"
                                    update-url="{{route('user-cphoto-json')}}"
                                    remove-url="{{route('user-cphoto-json')}}"
                                    v-bind:image-width="{{config('laradmin.cover_photo.width')}}"
                                    v-bind:image-height="{{config('laradmin.cover_photo.height')}}"
                                >
                                </laradmin-image-upload-single>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


@include('laradmin::user.partials.image.upload_single',[
                                                        'recommended_height'=>config('laradmin.cover_photo.height'),
                                                        'recommended_width'=>config('laradmin.cover_photo.width'),
                                                        ])
@endsection
