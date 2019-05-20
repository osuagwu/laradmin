@component('mail::message')
### This is a contact form message for {{$receiver->name}}

{{$userMessage->message}}



### This message is sent on behalf of {{$senderName}} ({{$senderEmail}})

{{ config('app.name') }}
@endcomponent
