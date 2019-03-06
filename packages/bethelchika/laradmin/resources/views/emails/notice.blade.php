@component('mail::message')
# Hello {{$name}}

{{$notice}}

@if(isset($action))
    @component('mail::button', ['url' => url($actionURL)])
    {{$action}}
    @endcomponent
@endif

Regards,<br>
{{ config('app.name') }}
@endcomponent
