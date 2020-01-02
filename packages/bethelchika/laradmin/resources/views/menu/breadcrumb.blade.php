{{-- 
    Prints a current breadcrumb or the given menu tag as breadcrumb
    INPUT
    $tags string {optional} The dot separated menu tags to be used as the root of the crumb.
    $from_children boolean {optional} Is the crumb items stored as the children of the item with the given tag?
--}}
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent">
        @foreach($laradmin->navigation->getBreadcrumbItems($tags??null,$from_children??false) as $bread_item)
            <li class="breadcrumb-item {{$loop->last?'active':''}}" @if($loop->last) aria-current="page" @endif > @if($loop->last) {{$bread_item->name}} @else<a href="{{$bread_item->getLink()}}" >{{$bread_item->name}}</a>@endif</li>
        @endforeach
    </ol>
</nav>