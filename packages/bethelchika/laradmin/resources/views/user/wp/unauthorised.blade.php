@extends('laradmin::user.layouts.app')


@section('content')
    <section class="section section-danger section-diffuse section-light-bg section-extra-padding-top section-extra-padding-bottom section-full-height   ">
        <div class="container{{$laradmin->assetManager->isContainerFluid('-fluid')}} text-center">
            <h1 class="page-title heading-4">You attempted to visit a protected page.</h1>
            <a href="{{route('home')}}">Go to homepage</a>
        </div>
    </section>      
@endsection
