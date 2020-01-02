{{--- Handles randering of Laradmin form fields for viewing

INPUT
$fields Collection of fields object 
    --}}

@foreach($fields as $field)
    @continue($field->isWriteOnly)
    @switch($field->type)
        @case($field::FIELDSET)
            <fieldset>
                @if($field->legend)<legend>{{$field->legend}}</legend>@endif
                @if($field->indexDescription)<div class="description">{{$field->indexDescription}}</div>@endif
                @include('laradmin::form.index_fields',['fields'=>$field->getFields()])
            </fieldset>
            @break
        @case($field::TEXT)
        @case($field::PASSWORD)
        @case($field::SELECT)
        @case($field::HTML)
        @case($field::RADIO)
        @case($field::TEXTAREA)
        @case($field::CHECKBOX)
            <div class="row {{$field->class}}">
                <div class="col-xs-6 col-md-3 "><div class="field">{{$field->label??ucfirst(str_replace('_',' ',$field->name))}} @if($field->unit) <em>{{$field->unit}}</em> @endif </div></div>
                <div class="col-xs-6 col-md-9 ">
                    @if($field->indexDescription)<div class="description">{{$field->indexDescription}}</div>@endif
                    <div class="value">
                    @if(str_is($field::PASSWORD,$field->type))
                        ********** 
                    @elseif(str_is($field::HTML,$field->type) or (str_is($field::TEXTAREA,$field->type) and $field->indexAllowHTML))
                        {!!$field->value!!}
                    @elseif(str_is($field::SELECT,$field->type) or str_is($field::RADIO,$field->type))
                        @foreach($field->options as $opt_fv=>$label)
                            @if(str_is($field->value,$opt_fv)){{$label}} @break @endif {{--print the label when found and break the loop--}}
                        @endforeach
                    @elseif(str_is($field::CHECKBOX,$field->type))
                        @foreach($field->value as $fv)
                            @foreach($field->options as $opt_fv=>$label)
                                @if(str_is($fv,$opt_fv)){{$label}} @break @endif {{--print the label when found and break the inner loop--}}
                            @endforeach
                            @if(!$loop->last),@endif {{--Separate list with a comma unless we are at the last one--}}
                        @endforeach
                    @else
                        {{$field->value}}
                    @endif
                    @if($field->editLink) 
                        <a class="fainted-04 " href="{{$field->editLink}}" title="Edit {{$field->label}}"> <i class="fas fa-edit"></i></a>
                    @else 
                        @if(isset($form) and $form->getEditLink()) <a class="fainted-04" href="{{$form->getEditLink()}}" title="Edit"> <i class="fas fa-pen"></i></a> @endif
                    @endif
                    </div>
                </div>
            </div>
            @break
        @case($field::IMAGE)
            <div class="row {{$field->class}}">
                <div class="col-xs-6 col-md-3 "><span class="">{{$field->label}} @if($field->unit) <em>{{$field->unit}}</em> @endif </span></div>
                <div class="col-xs-6 col-md-9 ">
                    <img class="img-sm" src="{{$field->value}}" alt="{{$field->label}}">
                    @if($field->indexDescription)<div class="description">{{$field->indexDescription}}</div>@endif 
                    @if($field->editLink) 
                        <a class="fainted-04 btn btn-primary btn-xs" href="{{$field->editLink}}" title="Edit {{$field->label}}"> <i class="fas fa-pen"></i></a>
                    @else 
                        @if(isset($form) and $form->getEditLink()) <a class="fainted-04" href="{{$form->getEditLink()}}" title="Edit"> <i class="fas fa-pen"></i></a> @endif
                    @endif
                </div>
            </div>
            @break 
        
        @default
            <div class="alert alert-warning">Attempt to print unknown field of type: '{{$field->type}}'</div>
    @endswitch
@endforeach
