{{--
* Open graph Meta for social media.
INPUT:
$metas['local'] string The local. Default('en_GB')
$metas['section'] The current section of website. Default (General)
$metas['url']    string The full url. Default(url('/'))
$metas['type']   string The type resource. Default(article) 
$metas['title']  string
$metas['description'] string
$metas['image'] string Image url
$metas['twitter_site'] string The website twitter handle name. Default(laradmin)
$metas['twitter_creator'] string The creator of this document. Default(laradmin)
$metas['twitter_domain'] string The website. Default(url('/'))

Push OG metas--}}
@push('meta')
<!--Social media metas-->
 <meta property="article:author" content="{{env('FACEBOOK_PAGE_URL','https://www.facebook.com/'.config('app.name', 'Laradmin'))}}">
 <meta property="article:section" content="{{$metas['section']??'General'}}">
 <meta property="fb:app_id" content="{{env('FACEBOOK_CLIENT_ID')}}" >
 <meta property="og:site_name" content="{{config('app.name', 'Laradmin')}}" >
 <meta property="og:locale" content="{{$metas['local']??'en_GB'}}">
 <meta property="og:url" content="{{$metas['url']??url('/')}}" >
 <meta property="og:type" content="{{$metas['type']??'article'}}" >
 <meta property="og:title" content="{{$metas['title']}}" >
 <meta property="og:description" content="{{$metas['description']}}" >
 @if(isset($metas['image']))
    <meta property="og:image" content="{{$metas['image']}}" >
    <meta property="og:image:alt" content="{{$metas['title']}}"> 
 @endif

 {{--twitter specific metas for summary_large_image --}}
 <meta name="twitter:card" content="summary_large_image">
 <meta name="twitter:site" content="{{'@'}}{{$metas['twitter_site']?? env('TWITTER_HANDLE','laradmin')}}">
 <meta name="twitter:title" content="{{$metas['title']}}">
 <meta name="twitter:description" content="{{$metas['description']}}">
 <meta name="twitter:creator" content="{{'@'}}{{$metas['twitter_creator']?? env('TWITTER_HANDLE','laradmin')}}">
 @if(isset($metas['image']))
    <meta name="twitter:image:src" content="{{$metas['image']}}">{{--TODO: do we realy need to image metas here--}}
    <meta name="twitter:image" content="{{$metas['image']}}">
    <meta name="twitter:image:alt" content="{{$metas['description']}}" >
 @endif
 @if(isset($metas['twitter_domain']))
    <meta name="twitter:domain" content="{{$metas['twitter_domain']?? url('/')}}">
 @endif


@endpush
