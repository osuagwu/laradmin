{{-- For comments see input_text.blade.php
    
--}}
<div class="form-group {{$errors->has($old_name??$name)? 'has-error':''}} {{$class??''}}">
    <label for="{{$id??$name}}" class="col-md-4 control-label">{{$label??ucfirst(str_replace('_',' ',$name))}}</label>
    <div class="col-md-6">
         @if(isset($description) and $description)<div class="description">{{$description}}</div>@endif
        <select class="form-control {{$control_class??''}}" id="{{$id??$name}}" name="{{$name}}"  
        style="{{$style??''}}">
            <option value="" {{!strcmp(old($old_name??$name,$value),'')? 'selected':''}}>Please select</option>
            @foreach($options as $option_key=>$option){{--Note, if options is not assoc, then the value of the <option> is integer starting from zero --}}
                <option value="{{$option_key}}" {{!strcmp(old($old_name??$name,$value),$option_key)? 'selected':''}}>{{$option}}</option>
            @endforeach
        </select>
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
    
</div>