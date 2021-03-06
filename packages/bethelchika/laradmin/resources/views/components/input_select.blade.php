<div class="form-group {{$errors->has($name)? 'has-error':''}} {{$class??''}}">
    <label for="{{$name}}" class="col-md-4 control-label">{{$label??ucfirst(str_replace('_',' ',$name))}}</label>
    <div class="col-md-6">
        <select class="form-control" id="{{$id??$name}}" name="{{$name}}"  
        style="{{$style??''}}">
            <option value="" {{!strcmp(old($name,$value),'')? 'selected':''}}>Please select</option>
            @foreach($options as $option_key=>$option)
                <option value="{{$option_key}}" {{!strcmp(old($name,$value),$option_key)? 'selected':''}}>{{$option}}</option>
            @endforeach
        </select>
        @if ($errors->has($name) or isset($help)) <p class="help-block">{{ str_finish($errors->first($name),'. ') }} <span class="">{{$help??''}}</span></p> @endif
    </div>
    
</div>