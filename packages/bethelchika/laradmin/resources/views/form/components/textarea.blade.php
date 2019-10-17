{{--
    Print a field of type 'textarea'

  For comments see input_text.blade.php
    
--}}

    <div class="form-group {{$errors->has($old_name??$name)? 'has-error':''}}  {{$class??''}}" >
            <label for="{{$id??$name}}" class="col-md-4 control-label">{{$label??ucfirst(str_replace('_',' ',$name))}} @if(isset($unit) and $unit) <em>{{$unit}}</em> @endif</label>
            <div class="col-md-6">
                @if(isset($description) and $description)<div class="description">{{$description}}</div>@endif
                <textarea id="{{$id??$name}}" class="form-control" name="{{$name}}"    {{ $required??''}} autofocus="" placeholder="{{$placeholder??'...'}}"  
                style="{{$style??''}}">{{old($old_name??$name,$value)}}</textarea>
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
