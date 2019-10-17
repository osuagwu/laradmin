{{-- Display a facebook page 
    $page_url string The facebook page url. Default to {{config('services.facebook.page_url')}}
    $page_name [optional] string. The name of the page. Default to sitename 
    $box_class [optional]: string Arbitrary Css class for the contaning box.
    --}}
<div class="fb-page {{$box_class??''}}" 
    data-href="{{$page_url??config('services.facebook.page_url')}}" 
    data-tabs="timeline" 
    data-width="" 
    data-height="" 
    data-small-header="false" 
    data-adapt-container-width="true" 
    data-hide-cover="false" 
    data-show-facepile="true"
    data-tabs="timeline,events,messages">

    <blockquote cite="{{$page_url??config('services.facebook.page_url')}}" class="fb-xfbml-parse-ignore">
        <a href="{{$page_url??config('services.facebook.page_url')}}">{{$page_name??config('app.name', 'Laradmin')}}</a>
    </blockquote>
</div>