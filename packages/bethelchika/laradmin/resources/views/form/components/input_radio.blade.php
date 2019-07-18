
<div class="form-group {{$errors->has($name)? 'has-error':''}} {{$class??''}}">
    <label  class="col-md-4 control-label">{{$label??ucfirst(str_replace('_',' ',$name))}}</label>
    <div class="col-md-6 ">
        @if(isset($description) and $description)<div class="description">{{$description}}</div>@endif
        @foreach($options as $option_key=>$option){{--Note, if options is not assoc, then the value of the <option> is integer starting from zero --}}
            <div class="radio">
                <label>
                    <input type="radio" name="{{$name}}"  value="{{$option_key}}" {{!strcmp(old($name,$value),$option_key)? 'checked':''}} {{$style??''}}>
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
        @if ($errors->has($name) or isset($help)) <p class="help-block">{{ str_finish($errors->first($name),'. ') }} <span class="">{{$help??''}}</span></p> @endif
    </div>
    
    
</div>


