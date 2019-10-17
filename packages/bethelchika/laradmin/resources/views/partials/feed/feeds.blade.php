
@push('head-styles')
<link href="{{ asset('vendor/laradmin/css/feed/feed.css') }}" rel="stylesheet">
@endpush
@push('footer-scripts-after-library')
    <script src="{{asset('vendor/laradmin/js/feed/components/feed.js')}}"></script>
@endpush


<laradmin-feeds  
    source-url='{{route("user-feed")}}' 
    box-class='{{$box_class??''}}' 
    v-bind:allow-fetch-on-scroll='{{$allow_fetch_on_scroll??'true'}}' 
    v-bind:realtime-interval='60000' 
    v-bind:initial-page-number='0' 
    v-bind:initial-has-more-pages='true' 
    v-bind:initial-feeds='{!! "[]" !!}'>
</laradmin-feeds>

