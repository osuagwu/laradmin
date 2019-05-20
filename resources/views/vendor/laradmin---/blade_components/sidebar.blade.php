{{--
    $content string:[optional] content of sidebar
    $menu_tag string:[optional] A tag of menu to be displays
    $class string:[optional] sidebar class
    $script $scrollbar_theme:[optional] scroll bar theme
    $include string:[optional] a partial to include
--}}
@include('laradmin::user.partials.sidebar.init')
<aside class="sidebar {{$class??''}}">
    {{-- sidebar control --}}
     
    <div class="sidebar-content mCustomScrollbar" data-mcs-theme="{{$scrollbar_theme??'minimal-dark'}}">
        <div class="sidebar-close-btn" title="Close sidebar">X</div>
        {{-- sidebar content --}}
        @if(isset($content)){{$content}} @endif
        @if(isset($menu)) 
            @include('laradmin::menu',['tag' => $menu_tag]) 
        @endif

        @if(isset($include)) 
            @include($include) 
        @endif

        {{$slot}}

        
    </div>
</aside>