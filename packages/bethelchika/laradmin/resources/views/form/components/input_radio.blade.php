
{{-- For comments see input_text.blade.php
    NOTE: To keep the control-label column but with empty content in it, just set 'label'=>''
        You can avoide the control-label column alltogether setting 'label'=.false.
    
--}}
<div class="form-group {{$errors->has($old_name??$name)? 'has-error':''}} {{$class??''}}">
    @if(isset($label) and $label===false)
    @else
        <label  class="col-md-4 control-label">{{$label??ucfirst(str_replace('_',' ',$name))}}</label>
    @endif
    <div class="col-md-6 ">
        @if(isset($description) and $description)<div class="description fainted-07">{{$description}}</div>@endif
        @foreach($options as $option_key=>$option){{--Note, if options is not assoc, then the value of the <option> is integer starting from zero --}}
            <div class="radio">
                <label>
                    <input type="radio" class="{{$control_class??''}}" name="{{$name}}"  value="{{$option_key}}" {{!strcmp(old($old_name??$name,$value),$option_key)? 'checked':''}} {{$style??''}}>
                    {{$option}}
                </label>
            </div>
        @endforeach
        {{-- <select class="form-control" id="{{$id??$name}}" name="{{$name}}"  
        style="{{$style??''}}">
            <option value="" {{!strcmp(old($name,$value),'')? 'selected':''}}>Please select</option>
            @foreach($options as $option_key=>$option)
                <option value="{{$option_key}}" {{!strcmp(old($name,$value),$option_key)? 'selected':''}}>{{$option}}</option>
            @endforeach
        </select> --}}
        @if ($errors->has($old_name??$name) or isset($help)) <p class="help-block">{{ ltrim(str_finish($errors->first($old_name??$name),'. '),'.') }} <span class="">{{$help??''}}</span></p> @endif
    </div>
    
    
</div>


