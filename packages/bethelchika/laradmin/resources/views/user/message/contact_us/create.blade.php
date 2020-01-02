@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.content_manager.stacks')
@push('head-styles')
<style>
.section.banner{
    background-image:   url("{{$section_first_bg??''}}");
}
</style>
@endpush
@section('content')

@component('laradmin::components.section',['type'=>'primary','isFullHeight'=>true, 'class'=>'banner','role'=>'main'])
    @slot('title')
        {{$pageTitle??'Contact us'}}
    @endslot
    
    @include ('laradmin::inc.msg_board')
    @include('laradmin::inc.email_confirmation_prompt')


    <div class="row">
        <div class="col-md-4">
            <h3 class=" heading-3">Address</h3>
            <p><strong>{{config('app.name')}}</strong></p>
            <p>116 Olakije Road <br /> Ikedury, Okaiwe<br /> OF 411 Y</p>
            <p>Tel: 079 459 45607</p>
            <p> E-mail: helpdesk@mywedsite.com </p>
        </div>
        <div class="col-md-8">
           <h3 class="text-center heading-3">Contact form</h3>
            
            @include('laradmin::user.message.contact_us.form') 
        </div>
    </div>  
@endcomponent            
@endsection