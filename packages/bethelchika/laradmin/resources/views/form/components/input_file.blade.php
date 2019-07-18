<div class="form-group {{$errors->has($name)? 'has-error':''}}  {{$class??''}}"   
        style="{{$style??''}}">
        <label for="{{$id??$name}}" class="col-md-4 control-label">{{$label??ucfirst(str_replace('_',' ',$name))}}</label>
        <div class="col-md-6">
            @if(isset($description) and $description)<div class="description">{{$description}}</div>@endif
            <input id="{{$id??$name}}" type="file" class="form-control" name="{{$name}}" value="{{old($name,$value)}}"   {{ $required??''}} autofocus="">
            @if ($errors->has($name) ) <p class="help-block">{{ str_finish($errors->first($name),'. ') }} <span class="">{{$help??''}}</span></p> @endif
        </div>
        {{$slot}}
</div>