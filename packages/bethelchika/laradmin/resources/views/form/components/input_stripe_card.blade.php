{{--
    Print a field of type 'stripe' for Stripe card element.

    INPUT:
    $name string Name of field. This is used as id if id is not provided.

    $class string (optional) Css class
    $label string (optional) The label of field.
    $help string (optional) Help text
    $id string (optional) The html element ID of field
    $style string (optional) Inline style for the input element
    $unit string The unit of the field (e.g $,£,cm, etc).
    $description string Text to give more description to the input
    --}}

<div class="form-group  {{$class??''}}" >
        <label style="text-align: left" for="{{$id??$name}}" class="col-md-12 control-label">{{$label??ucfirst(str_replace('_',' ',$name))}} @if(isset($unit) and $unit) <em>{{$unit}}</em> @endif</label>
        <div class="col-md-12">
            @if(isset($description) and $description)<div class="description">{{$description}}</div>@endif
            <div id="{{$id??$name}}"  class="form-control"  style="{{$style??''}}">
            </div>
                          
            @if(isset($help))
            <p class="help-block">
                <span >{{$help}}</span>
            </p>
            @endif
                       
        </div>
        {{$slot??''}}
</div>