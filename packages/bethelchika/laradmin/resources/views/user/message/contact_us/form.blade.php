{{--  The form for contact form
    [Inputs]
$returnToUrl string, the URL that this form should return to after success
$parent_id Integer The parent message id for message that will be created from the contact form
--}}
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