{{--- Handles randering of Laradmin form fields

INPUT
$fileds Collection of fields object 
    --}}

@foreach($fields as $field)
    @switch($field->type)
        @case($field::FIELDSET)
            <fieldset>
                @if($field->legend)<legend>{{$field->legend}}</legend>@endif
                @include('laradmin::form.fields',['fields'=>$field->getFields()])
            </fieldset>
            @break
        @case($field::TEXT)
            @include('laradmin::form.components.input_text',['name'=>$field->name,'label'=>$field->label,'value'=>$field->value,'help'=>$field->help,'placeholder'=>$field->placeholder,'unit'=>$field->unit,'class'=>$field->class,'style'=>$field->style])
            @break
        @case($field::PASSWORD)
            @include('laradmin::form.components.input_password',['name'=>$field->name,'label'=>$field->label,'value'=>$field->value,'help'=>$field->help,'placeholder'=>$field->placeholder,'unit'=>$field->unit,'class'=>$field->class,'style'=>$field->style])
            @break
        @case(2)
            
        @case($field::TEXTAREA)
            @include('laradmin::form.components.textarea',['name'=>$field->name,'label'=>$field->label,'value'=>$field->value,'help'=>$field->help,'placeholder'=>$field->placeholder,'unit'=>$field->unit,'class'=>$field->class,'style'=>$field->style])
            @break
        @default
            
    @endswitch
@endforeach
