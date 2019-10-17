{{--
    Print a field of type 'text'

    INPUT:
    $name string Name of field
    $old_name string (optional) The name to be used to access the previous value. This is 
                                only important when the fieldname has the square brackets in 
                                order to create array in php. e.g for a name oranges[] then
                                you can set the $old_name=oranges.0 to access the first 
                                index etc.See Laravel docs for more details or see 
                                https://stackoverflow.com/questions/42050732/old-input-for-array
    $value string The value of the input filed
    $required boolean (optional) Is this field required
    $class string (optional) Css class
    $label string (optional) The label of field.
    $help string (optional) Help text
    $id string (optional) The html element ID of field
    $style string (optional) Inline style for the input element
    $placeholder string (optional) The input placeholder
    $unit string The unit of the field (e.g $,£,cm, etc).
    $description string Text to give more description to the input
    --}}

<div class="form-group {{$errors->has($old_name??$name)? 'has-error':''}}  {{$class??''}}" >
        <label for="{{$id??$name}}" class="col-md-4 control-label">{{$label??ucfirst(str_replace('_',' ',$name))}} @if(isset($unit) and $unit) <em>{{$unit}}</em> @endif</label>
        <div class="col-md-6">
            @if(isset($description) and $description)<div class="description">{{$description}}</div>@endif
            <input id="{{$id??$name}}" type="text" class="form-control" name="{{$name}}" value="{{old($old_name??$name,$value)}}"   {{ $required??''}} autofocus="" placeholder="{{$placeholder??'...'}}"  
             style="{{$style??''}}">
            @if ($errors->has($old_name??$name) or isset($help)) 
                <p class="help-block">
                    @if($errors->has($old_name??$name))
                        {{ str_finish($errors->first($old_name??$name),'.') }}
                    @endif
                    @if(isset($help))
                        <span >{{$help}}</span>
                    @endif
                </p>
            @endif
            
        </div>
        {{$slot??''}}
</div>