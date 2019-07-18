
<div class="form-group {{$errors->has($name)? 'has-error':''}} {{$class??''}}">
    <label  class="col-md-4 control-label">{{$label??ucfirst(str_replace('_',' ',$name))}}</label>
    <div class="col-md-6 ">
        @if(isset($description) and $description)<div class="description">{{$description}}</div>@endif
        @foreach($options as $option_key=>$option){{--Note, if options is not assoc, then the value of the <option> is integer starting from zero --}}
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="{{$name}}@if(count($options)>1)[]@endif"  value="{{$option_key}}" @if(is_array(old($name,$value))) @foreach(old($name,$value) as $opt) @if(!strcmp($opt,$option_key)) {{'checked'}} @break @endif @endforeach @else @if(!strcmp(old($name,$value),$option_key)) {{'checked'}}@endif @endif {{$style??''}}>
                    @if(count($options)>1){{$option}}@endif
                </label>
            </div>
        @endforeach
       
        @if ($errors->has($name) or isset($help)) <p class="help-block">{{ str_finish($errors->first($name),'. ') }} <span class="">{{$help??''}}</span></p> @endif
    </div>
    
    
</div>

