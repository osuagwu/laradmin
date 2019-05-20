{{--- Handles randering of Laradmin form fields for editing

INPUT
$fileds Collection of fields object 
    --}}

@foreach($fields as $field)
    @switch($field->type)
        @case($field::FIELDSET)
            <fieldset>
                @if($field->legend)<legend>{{$field->legend}}</legend>@endif
                @include('laradmin::form.edit_fields',['fields'=>$field->getFields()])
            </fieldset>
            @break
        @case($field::TEXT)
            @include('laradmin::form.components.input_text',['name'=>$field->name,'label'=>$field->label,'value'=>$field->value,'help'=>$field->help,'placeholder'=>$field->placeholder,'unit'=>$field->unit,'class'=>$field->class,'style'=>$field->style])
            @break
        @case($field::PASSWORD)
            @include('laradmin::form.components.input_password',['name'=>$field->name,'label'=>$field->label,'value'=>$field->value,'help'=>$field->help,'placeholder'=>$field->placeholder,'unit'=>$field->unit,'class'=>$field->class,'style'=>$field->style])
            @break      
        @case($field::SELECT)
            @include('laradmin::form.components.input_select',['name'=>$field->name,'label'=>$field->label,'value'=>$field->value,'unit'=>$field->unit,'options'=>$field->options,'placeholder'=>$field->placeholder,'unit'=>$field->unit,'class'=>$field->class,'style'=>$field->style])
            @break  
        @case($field::RADIO)  
            @include('laradmin::form.components.input_radio',['name'=>$field->name,'label'=>$field->label,'value'=>$field->value,'unit'=>$field->unit,'options'=>$field->options,'placeholder'=>$field->placeholder,'unit'=>$field->unit,'class'=>$field->class,'style'=>$field->style])
            @break  
        @case($field::CHECKBOX)  
            @include('laradmin::form.components.input_checkbox',['name'=>$field->name,'label'=>$field->label,'value'=>$field->value,'unit'=>$field->unit,'options'=>$field->options,'placeholder'=>$field->placeholder,'unit'=>$field->unit,'class'=>$field->class,'style'=>$field->style])
            @break
        @case($field::TEXTAREA)
            @include('laradmin::form.components.textarea',['name'=>$field->name,'label'=>$field->label,'value'=>$field->value,'help'=>$field->help,'placeholder'=>$field->placeholder,'unit'=>$field->unit,'class'=>$field->class,'style'=>$field->style])
            @break
        @case($field::IMAGE)
            @include('laradmin::form.components.input_file',['name'=>$field->name,'label'=>$field->label,'value'=>$field->value,'help'=>$field->help,'placeholder'=>$field->placeholder,'unit'=>$field->unit,'class'=>$field->class,'style'=>$field->style])
            @break
        @case($field::HTML)
            {{--Do not display HTML type in edit mode for now--}} 
            <div class="alert alert-warning"><strong>Not  allowed: </strong> Attempt to display HTML filed type in edit mode</div>
            @break
        @default
            
    @endswitch
@endforeach
