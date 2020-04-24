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
                                    source-url="{{route('user-avatar-json')}}"
                                    upload-url="{{route('user-avatar-json')}}"
                                    update-url="{{route('user-avatar-json')}}"
                                    remove-url="{{route('user-avatar-json')}}"
                                    v-bind:image-width="{{config('laradmin.avatar.width')}}"
                                    v-bind:image-height="{{config('laradmin.avatar.height')}}"
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
                                                        'recommended_height'=>config('laradmin.avatar.height'),
                                                        'recommended_width'=>config('laradmin.avatar.width'),
                                                        ])
@endsection
