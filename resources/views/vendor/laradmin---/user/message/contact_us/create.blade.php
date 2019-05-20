@extends('laradmin::user.layouts.app')
@push('head-styles')
<style>
.section.banner{
    background-image:   url("{{$section_first_bg??''}}");
}
</style>
@endpush
@section('content')

@component('laradmin::blade_components.section',['type'=>'primary','isFirst'=>true,'isFullHeight'=>true, 'class'=>'banner','role'=>'main'])
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
            
            <form class="form-horizontal" role="form" method="post" action="{{route('contact-us-store')}}">   
                        {{ csrf_field() }}
                @component('laradmin::blade_components.input_text',['name'=>'first_name','value'=>'','required'=>'required','label'=>'First name'])
                @endcomponent

                @component('laradmin::blade_components.input_text',['name'=>'last_name','value'=>'','label'=>'Last name'])
                @endcomponent

                @component('laradmin::blade_components.input_text',['name'=>'title','value'=>''])
                @endcomponent

                @component('laradmin::blade_components.input_text',['name'=>'your_email','value'=>'','required'=>'required','label'=>'Your email'])
                @endcomponent
                

                @component('laradmin::blade_components.input_text',['name'=>'subject','value'=>'','required'=>'required'])
                @endcomponent 

                @component('laradmin::blade_components.textarea',['name'=>'message','value'=>''])
                @endcomponent 
                
                <input type="hidden" name="parent_id" value="{{$parent_id}}" />
                <input type="hidden" name="return_to_url" value="{{old('return_to_url',$returnToUrl)}}" />

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                    
                        <a class="btn btn-subtle" href="{{$returnToUrl}}">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Send
                        </button>
                    </div>
                </div>
            </form>  
        </div>
    </div>  
@endcomponent            
@endsection