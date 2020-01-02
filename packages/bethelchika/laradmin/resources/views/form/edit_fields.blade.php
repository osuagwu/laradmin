{{--- Handles randering of Laradmin form fields for editing

INPUT
$fileds Collection of fields object 
    --}}

@foreach($fields as $field)
    @continue($field->isReadOnly)
    @switch($field->type)
        @case($field::FIELDSET)
            <fieldset>
                @if($field->legend)<legend>{{$field->legend}}</legend>@endif
                @include('laradmin::form.edit_fields',['fields'=>$field->getFields()])
            </fieldset>
            @break
        @case($field::TEXT)
            @include('laradmin::form.components.input_text',['id'=>$field->id,'name'=>$field->name,'label'=>$field->label,'value'=>$field->value,'help'=>$field->help,'placeholder'=>$field->placeholder,'unit'=>$field->unit,'class'=>$field->class,'style'=>$field->style,'description'=>$field->editDescription])
            @break
        @case($field::PASSWORD)
            @include('laradmin::form.components.input_password',['id'=>$field->id,'name'=>$field->name,'label'=>$field->label,'value'=>$field->value,'help'=>$field->help,'placeholder'=>$field->placeholder,'unit'=>$field->unit,'class'=>$field->class,'style'=>$field->style,'description'=>$field->editDescription])
            @break      
        @case($field::SELECT)
            @include('laradmin::form.components.input_select',['id'=>$field->id,'name'=>$field->name,'label'=>$field->label,'value'=>$field->value,'unit'=>$field->unit,'options'=>$field->options,'placeholder'=>$field->placeholder,'unit'=>$field->unit,'class'=>$field->class,'style'=>$field->style,'description'=>$field->editDescription])
            @break  
        @case($field::RADIO)  
            @include('laradmin::form.components.input_radio',['id'=>$field->id,'name'=>$field->name,'label'=>$field->label,'value'=>$field->value,'unit'=>$field->unit,'options'=>$field->options,'placeholder'=>$field->placeholder,'unit'=>$field->unit,'class'=>$field->class,'style'=>$field->style,'description'=>$field->editDescription])
            @break  
        @case($field::CHECKBOX)  
            @include('laradmin::form.components.input_checkbox',['id'=>$field->id,'name'=>$field->name,'label'=>$field->label,'value'=>$field->value,'unit'=>$field->unit,'options'=>$field->options,'placeholder'=>$field->placeholder,'unit'=>$field->unit,'class'=>$field->class,'style'=>$field->style,'description'=>$field->editDescription])
            @break
        @case($field::TEXTAREA)
            @include('laradmin::form.components.textarea',['id'=>$field->id,'name'=>$field->name,'label'=>$field->label,'value'=>$field->value,'help'=>$field->help,'placeholder'=>$field->placeholder,'unit'=>$field->unit,'class'=>$field->class,'style'=>$field->style,'description'=>$field->editDescription,'is_rich_text'=>$field->isRichText])
            @break
        @case($field::IMAGE)
            @include('laradmin::form.components.input_file',['id'=>$field->id,'name'=>$field->name,'label'=>$field->label,'value'=>$field->value,'help'=>$field->help,'placeholder'=>$field->placeholder,'unit'=>$field->unit,'class'=>$field->class,'style'=>$field->style,'description'=>$field->editDescription])
            @break
        @case($field::HTML)
            {{--Do not display HTML type in edit mode for now--}} 
            <div class="alert alert-warning"><strong>Not  allowed: </strong> Attempt to display HTML field type in edit mode</div>
            @break
        @default
            
    @endswitch
@endforeach 
