{{-- 
    Prints a current breadcrumb or the given menu tag as breadcrumb
    INPUT
    $tags string {optional} The dot separated menu tags to be used as the root of the crumb.
--}}
<ol class="breadcrumb bg-transparent">
    @foreach($laradmin->navigation->getBreadcrumbItems($tags??null) as $bread_item)
        <li class="breadcrumb-item {{$loop->last?'active':''}}">@if($loop->last) {{$bread_item->name}} @else<a href="{{$bread_item->getLink()}}" >{{$bread_item->name}}</a>@endif</li>
    @endforeach
</ol>