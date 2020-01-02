{{-- For comments see input_text.blade.php
    
--}}
<div class="form-group {{$errors->has($old_name??$name)? 'has-error':''}}  {{$class??''}}"   
        style="{{$style??''}}">
        <label for="{{$id??$name}}" class="col-md-4 control-label">{{$label??ucfirst(str_replace('_',' ',$name))}}</label>
        <div class="col-md-6">
            @if(isset($description) and $description)<div class="description">{{$description}}</div>@endif
            <input id="{{$id??$name}}" type="file" class="form-control {{$control_class??''}}" name="{{$name}}" value="{{old($old_name??$name,$value)}}"   {{ $required??''}} autofocus="">
            @if ($errors->has($old_name??$name) ) <p class="help-block">{{ str_finish($errors->first($old_name??$name),'. ') }} <span class="">{{$help??''}}</span></p> @endif
        </div>
        {{$slot??''}}
</div>