 {{--Push OG metas--}}
@push('meta')
 <meta property="article:author" content="https://www.facebook.com/webferendum">
 <meta property="article:section" content="World">
 <meta property="fb:app_id" content="{{env('FACEBOOK_CLIENT_ID')}}" />
 <meta property="og:site_name" content="{{config('app.name', 'Webferendum')}}" />
 <meta property="og:locale" content="en_GB">
 <meta property="og:url" content="{{url('/comicpic/show/'.$comicpic->id)}}" />
 <meta property="og:type" content="article" />
 <meta property="og:title" content="{{$comicpic->title}}" />
 <meta property="og:description" content="{{$comicpic->description}}" />
 <meta property="og:image" content="{{Storage::disk('public')->url($comicpic->medias[0]->getFullName())}}" />
 <meta property="og:image:alt" content="{{$comicpic->title}}"> 
 
 {{--twitter specific metas--}}
 <meta name="twitter:card" content="summary_large_image">
 <meta name="twitter:site" content="@webferendum">
 <meta name="twitter:title" content="{{$comicpic->title}}">
 <meta name="twitter:description" content="{{$comicpic->description}}">
 <meta name="twitter:creator" content="@webferendum">
 <meta name="twitter:image" content="{{Storage::disk('public')->url($comicpic->medias[0]->getFullName())}}">
 <meta name="twitter:image:alt" content="{{$comicpic->description}}" />
 <meta name="twitter:domain" content="www.webferendum.com">
@endpush