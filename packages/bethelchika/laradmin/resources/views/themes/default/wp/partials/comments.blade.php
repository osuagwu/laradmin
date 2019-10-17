{{-- 
COmments Vue components loader --}}
@push('footer-scripts-after-library')
    <script src="{{asset('vendor/laradmin/js/wp/components/comments.js')}}"></script>
@endpush


<laradmin-wp-comments  
    v-bind:post-id={{$post_id}}
    source-url='{{route("post-comments")}}' 
    box-class='{{$box_class??''}}' 
    v-bind:allow-fetch-on-scroll='{{$allow_fetch_on_scroll??'true'}}' 
    v-bind:realtime-interval='70000' 
    v-bind:initial-page-number='0' 
    v-bind:initial-has-more-pages='true' 
    v-bind:initial-comments='{!! "[]" !!}'>
</laradmin-wp-comments>

