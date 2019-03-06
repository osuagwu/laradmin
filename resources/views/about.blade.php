@extends('layouts.app')
@push('head-styles')
<style>
.section.hero{
    background-image: url(https://www.paypalobjects.com/digitalassets/c/website/marketing/emea/pl/pl/personal/Personal_Hero.jpg);
}  
</style>
@endpush
@section('content')
<section class="section section-primary section-first  section-full-height hero hero-super">
    <div class="section-overlay ">
        <div class="container">
            <div class="hero-headline extra-padding-top text-center extra-padding-bottom">

                <h1 class="hero-headline-text">
                    This is a main hero text that you can just see as soon as you jump on this website

                </h1>
                <a href="#" class="btn-hero btn-hero-clean">Block link</a>
            </div>
        </div>
    </div>
</section>
<section class="section section-default section-extra-padding-bottom">     
    <div class="container">
        <h2 class="content-title text-center ">This is a section titleThis is a section titleThis is a y</h2>
        <div class="row">
            <div class="col-md-4 block-content">
                <h3 class="block-title">
                    <span class="block-icon"> <i class="fa fa-phone"></i></span> 
                    <span>This is the header</span>
                </h3>
                <p class="block-para"> This is a test paragrapThis is a test paragrap This is a test paragrap</p>
                <a href="#" class="block-link btn-skeleton-primary-inverse">Block link</a>
            </div>

            <div class="col-md-4 block-content">
                <h3 class="block-title">
                    <span class="block-icon"> <i class="fa fa-car"></i></span> 
                    <span>This is the header</span>
                </h3>
                <p class="block-para"> This is a test paragrap This is a test paragrap This is a test paragrap</p>
                <a href="#" class="block-link btn-skeleton-warning-inverse">Block link</a>
            </div>
            <div class="col-md-4 block-content last">
                <h3 class="block-title">
                    <span class="block-icon"> <i class="fa fa-envelope"></i></span> 
                    <span>This is the header</span>
                </h3>
                <p class="block-para"> This is a test paragrapThis is a test paragrap This is a test paragrap</p>
                <a href="#" class="block-link btn-skeleton-inverse">Block link</a>
            </div>
        </div>
    </div>    
</section>

<section class="section section-info section-extra-padding-bottom">     
    <div class="container">
            <h2 class="heading-3 text-center section-extra-padding-top">This is a section title This is a section title This is a y <a class="btn btn-primary  inline-space" >Click here</a></h2>
    </div>
</section>
@endsection