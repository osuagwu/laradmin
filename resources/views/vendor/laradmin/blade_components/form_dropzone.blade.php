{{--
    $help string:[optional] help string for the form
    $action string: form action
    $fields string : [optional] html elements inside the form
    $script string:[optional] scripts  which can include the dropzone options
    $multiple boolean: [optional] : is this a multiple upload
--}}


<form id="{{$id??$name}}"  action="{{$action}}" class="dropzone form-horizontal" role="form" method="post" enctype="multipart/form-data">
    {{ csrf_field() }}
    @if(isset($fields)){!!$fields!!} @endif
    <div class="fallback ">
        <input name="{{$name}}" type="file" @if(isset($multiple) && $multiple) multiple @endif />
    </div>
    {!!$form_content!!}
</form>
@if(isset($help))<p class="help-block">{{$help}}</p>@endif
@push('head-styles')
    <link href="{{ asset('vendor/laradmin/vendor/dropzone/dropzone.min.css') }}" rel="stylesheet">
@endpush
@push('footer-scripts')
    <script src="{{asset('vendor/laradmin/vendor/dropzone/dropzone.min.js')}}"></script>

    @if(isset($script)) {!!$script!!} @endif
@endpush


{{--
<div id="{{$id??$name}}" class="dropzone">
        
</div>
@if(isset($help))<p class="help-block">{{$help}}</p>@endif

@push('head-styles')
    <link href="{{ asset('vendor/laradmin/vendor/dropzone/dropzone.min.css') }}" rel="stylesheet">
@endpush
@push('footer-scripts')
    <script src="{{asset('vendor/laradmin/vendor/dropzone/dropzone.min.js')}}"></script>
    <script>
        var myDropzone = new Dropzone("div#{{$id??$name}}", { 
            url: "{{$action}}",
            uploadMultiple:@if(isset($multiple) && $multiple) true @else false @endif,
            paramName:{{$name}},
            maxFiles:50,
            addRemoveLinks:true,
            headers: {
                'x-csrf-token': document.querySelectorAll('meta[name=csrf-token]')[0].getAttributeNode('content').value,
              },
        });
    </script>
@endpush
--}}