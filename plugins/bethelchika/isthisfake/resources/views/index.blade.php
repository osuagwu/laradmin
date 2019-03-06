@extends('layouts.app')

@section('content')

<section class="section section-default section-first section-extra-padding-bottom">     
    <div class="container">
        <h1 class="section-title text-center "><img style="width:80px;" src="/vendor/isthisfake/img/logo.jpg" alt="Is this Fake logo" /><br />  Welcome to IsThisFake?</h1>
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