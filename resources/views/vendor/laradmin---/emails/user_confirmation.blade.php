@component('mail::message')
# Hello {{$user->name}}

We are pleased that you have created an account on {{ config('app.name') }}. Please use the following instruction to complete your registration.

@component('mail::button', ['url' => $confirmationLink])
Confirm
@endcomponent

You can also copy and paste the following link on the address bar of your web browser:
{{$confirmationLink}}

Thank you,<br>
{{ config('app.name') }}
@endcomponent
