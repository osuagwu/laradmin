<div class="form-group {{$errors->has($name)? 'has-error':''}}  {{$class??''}}" >
        <label for="{{$id??$name}}" class="col-md-4 control-label">{{$label??ucfirst(str_replace('_',' ',$name))}}</label>
        <div class="col-md-6">
            <input id="{{$id??$name}}" type="text" class="form-control" name="{{$name}}" value="{{old($name,$value)}}"   {{ $required??''}} autofocus="" placeholder="{{$placeholder??'...'}}"  
             style="{{$style??''}}">
            @if ($errors->has($name) or isset($help)) <p class="help-block">{{ str_finish($errors->first($name),'. ') }} <span class="">{{$help??''}}</span></p> @endif
            {{-- @if (isset($help)) <span class="help-block">{{ $help }}</span> @endif --}}
        </div>
        {{$slot}}
</div>