{{--
    Print a field of type 'password'

    INPUT:
    $name string Name of field
    $value strgig The value of the input filed
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

{{--  <div class="form-group {{$errors->has($name)? 'has-error':''}} {{$class??''}}" >
        <label for="{{$id??$name}}" class="col-md-4 control-label">{{$label??ucfirst(str_replace('_',' ',$name))}}</label>
        <div class="col-md-6">
            <input id="{{$id??$name}}" type="password" class="form-control" name="{{$name}}" value=""  {{ $required??''}} autofocus="" placeholder="{{$placeholder??'............'}}"  
            style="{{$style??''}}">
            @if ($errors->has($name) or isset($help)) <p class="help-block">{{ str_finish($errors->first($name),'. ') }} <span class="">{{$help??''}}</span></p> @endif
        </div>
        {{$slot}}
</div>
  --}}


    <div class="form-group {{$errors->has($name)? 'has-error':''}}  {{$class??''}}" >
            <label for="{{$id??$name}}" class="col-md-4 control-label">{{$label??ucfirst(str_replace('_',' ',$name))}} @if(isset($unit) and $unit) <em>{{$unit}}</em> @endif</label>
            <div class="col-md-6">
                @if(isset($description) and $description)<div class="description">{{$description}}</div>@endif
                <input id="{{$id??$name}}" type="password" class="form-control" name="{{$name}}" value="{{old($name,$value)}}"   {{ $required??''}} autofocus="" placeholder="{{$placeholder??'...'}}"  
                 style="{{$style??''}}">
                @if ($errors->has($name) or isset($help)) 
                    <p class="help-block">
                        @if($errors->has($name))
                            {{ str_finish($errors->first($name),'.') }}
                        @endif
                        @if(isset($help))
                            <span >{{$help}}</span>
                        @endif
                    </p>
                @endif
                
            </div>
            {{$slot}}
    </div>