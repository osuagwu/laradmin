@component('mail::message')
### This message is for {{$receiver->name}}

{!!$message!!}



### This message is sent on behalf of {{$sender->name}}@if($adminSender) by {{$adminSender->name}} @endif.

{{ config('app.name') }}
@endcomponent
