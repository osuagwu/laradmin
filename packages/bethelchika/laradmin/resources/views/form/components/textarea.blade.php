{{--
    Print a field of type 'textarea'

  For comments see input_text.blade.php
  Here are Inputs specific to texterea.
  $is_rich_text boolean (optional=false) When true WYSIWYG i used for editing
    
--}}

    <div class="form-group {{$errors->has($old_name??$name)? 'has-error':''}}  {{$class??''}} " >
            <label for="{{$id??$name}}" class="col-md-4 control-label">{{$label??ucfirst(str_replace('_',' ',$name))}} @if(isset($unit) and $unit) <em>{{$unit}}</em> @endif</label>
            <div class="col-md-6">
                @if(isset($description) and $description)<div class="description">{{$description}}</div>@endif
                <textarea id="{{$id??$name}}" class="form-control {{$control_class??''}} @if(isset($is_rich_text) and $is_rich_text) text-editor @endif" name="{{$name}}"    {{ $required??''}} autofocus="" placeholder="{{$placeholder??'...'}}"  
                style="{{$style??''}}">
                    @if(isset($is_rich_text) and $is_rich_text)
                    {!!old($old_name??$name,$value)!!}
                    @else 
                    {{old($old_name??$name,$value)}}
                    @endif
                </textarea>
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

@if(isset($is_rich_text) and $is_rich_text) 
    @push('footer-scripts')
    <script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector: '.text-editor',
            height: 200,
            menubar: false,
            branding: false,
            plugins: [
                'lists link image preview',
            ],
            toolbar: 'undo redo | formatselect | bold italic  | bullist numlist',
            block_formats: 'Paragraph=p;Heading 5=h5;Heading 6=h6;',
        });
    </script>
    @endpush
@endif