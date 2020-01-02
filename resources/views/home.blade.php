@extends('laradmin::user.layouts.app')
@push('head-styles')
<style>
.section.banner{
  background-image:   url("https://www.paypalobjects.com/webstatic/en_GB/mktg/wright/partners_and_developers/Partner-Developers-Page_Website-hero.jpg");
}  
</style>    
@endpush 
@section('content')
<section class="section section-subtle section-diffuse section-light-bg section-diffuse-no-shadow section-extra-padding-bottom">
    
    <div class="container">
        
        <div class="row">
            <div class="col-md-6 col-md-offset-3" >
                <h1 class="heading-1 content-title text-center">What's happening?</h1>
                @include('laradmin::partials.feed.feeds',['allow_fetch_on_scroll'=>'false', 'box_class'=>'flat-design'])
            </div>
        </div>
    </div>
    
</section>
<section class="section section-primary  section-full-height banner ">
    <div class="section-overlay ">
        <div class="container">
            <div class="banner-headline extra-padding-top text-center ">
                    
                <h1 class="banner-headline-text">
                    Short and snappy!
                </h1>
            </div>           
            <div class="row ">
                <div class="col-md-4 col-md-offset-1 banner-block-content compact first" >
                        <h2 class="block-title heading-1">
                            
                            <span>This is the header</span>
                        </h2>
                        <p class="block-para">  This is a test paragrap showing how text can be added under the  header</p>
                        <a href="#" class="btn-block-content btn-skeleton-warning-inverse">Block link</a>
                    
                </div>
                
                <div class="col-md-4 col-md-offset-2 banner-block-content compact last">
                        <h2 class="block-title heading-1">
                            
                            <span>This is the header</span>
                        </h2>
                        <p class="block-para">  This is a test paragrap showing how text can be added under the  header</p>
                        <a href="#" class="btn-block-content btn-skeleton-primary-inverse">Block link</a>
                    
                </div>

            </div>
        </div>
    </div>
</section>
<section class="section section-default section-extra-padding-bottom">     
    <div class="container">
        <h2 class="section-title text-center ">This is a section title. This is a section title. Yes it is</h2>
        <div class="row">
            <div class="col-md-4 block-content">
                <h3 class="block-title">
                    <span class="block-icon"> <i class="fa fa-phone"></i></span> 
                    <span>This is the header</span>
                </h3>
                <p class="block-para"> This is a test paragrap showing how text can be added under the  header</p>
                <a href="#" class="block-link btn-skeleton-primary-inverse">Block link</a>
            </div>

            <div class="col-md-4 block-content">
                <h3 class="block-title">
                    <span class="block-icon"> <i class="fa fa-car"></i></span> 
                    <span>This is the header</span>
                </h3>
                <p class="block-para">  This is a test paragrap showing how text can be added under the  header</p>
                <a href="#" class="block-link btn-skeleton-warning-inverse">Block link</a>
            </div>
            <div class="col-md-4 block-content last">
                <h3 class="block-title">
                    <span class="block-icon"> <i class="fa fa-envelope"></i></span> 
                    <span>This is the header</span>
                </h3>
                <p class="block-para">  This is a test paragrap showing how text can be added under the  header</p>
                <a href="#" class="block-link btn-skeleton-inverse">Block link</a>
            </div>
        </div>
    </div>    
</section>

<section class="section section-info section-extra-padding-bottom">     
    <div class="container">
            <h2 class="heading-3 text-center section-extra-padding-top"> This is a test paragrap showing how text can be added under the  header<a class="btn btn-primary  inline-space" >Click here</a></h2>
    </div>
</section>
@endsection