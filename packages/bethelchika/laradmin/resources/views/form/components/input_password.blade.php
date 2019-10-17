{{--
    Print a field of type 'password'

     For comments see input_text.blade.php
    
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


    <div class="form-group {{$errors->has($old_name??$name)? 'has-error':''}}  {{$class??''}}" >
            <label for="{{$id??$name}}" class="col-md-4 control-label">{{$label??ucfirst(str_replace('_',' ',$name))}} @if(isset($unit) and $unit) <em>{{$unit}}</em> @endif</label>
            <div class="col-md-6">
                @if(isset($description) and $description)<div class="description">{{$description}}</div>@endif
                <input id="{{$id??$name}}" type="password" class="form-control" name="{{$name}}" value="{{old($old_name??$name,$value)}}"   {{ $required??''}} autofocus="" placeholder="{{$placeholder??'...'}}"  
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