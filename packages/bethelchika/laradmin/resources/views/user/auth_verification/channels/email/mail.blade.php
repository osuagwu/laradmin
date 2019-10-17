@component('mail::message')
# Hello {{$user->name}}

Your verification code for {{ config('app.name') }} is:

{{$code}}

Please not that this code expires in {{ intval(intval(config('laradmin.auth_verification_code_expiry'))/(60*60)) }} hours.

Thank you,<br>
{{ config('app.name') }}
@endcomponent
