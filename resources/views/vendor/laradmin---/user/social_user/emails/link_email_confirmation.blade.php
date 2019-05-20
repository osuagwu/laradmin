@component('mail::message')
# Hello {{$user->name}}

The following email has been linked to your account at {{ config('app.name') }}.

{{$linkEmail}}

Please use the information below to complete the linking process.

@component('mail::button', ['url' => $confirmationLink])
Confirm
@endcomponent

You can also copy and paste the following link on the address bar of your web browser:
{{$confirmationLink}}

Thank you,<br>
{{ config('app.name') }}
@endcomponent
