{{----
    Prints Minor nav which shown be below the main nav. 
    This uses a bootstrap navbar class but without the container class by deafult to avoid mistakenly nesting containers when this partial in included in a view;

    INPUTS
    $root_tag string The root tag of minor nav. (e.g $root_tag=false disables root tag behaviour; $root_tag='primary' for minor menus under a Menu with tag 'primary'; $root_tag='primary.services' for minor nav under a menu with tag 'primary' and under menu item with tag 'services' ).
    $left_menu_tag string  [optional]  Tag or dot separated manu tag that goes to the left
    $right_menu_tag string [optional]  Tag or dot separated manu tag that goes to the right
    $title= string [optional]  The title of the bar . 
    $with_container boolean [optional]  Set to false to remove bootstrap container inside the minor nav (Use when you already have container wrapping the output of this file).
    $scheme string [optional]  The scheme to used for nav tag. e.g 'primary' which makes the minor nav a a primary look and feel
    $class string [optional] The class added to the nav tag
--}}
@php



// set default root tag
if(!isset($root_tag)){
    $root_tag=$laradmin->navigation->getMinorNavTag();//
}

//Do not display if menu is empty
$is_empty=$root_tag?$laradmin->navigation->isEmptyTags($root_tag) : true;
if($is_empty and isset($left_menu_tag)){
    $is_empty=$laradmin->navigation->isEmptyTags($left_menu_tag);
}
if($is_empty and isset($right_menu_tag)){
    $is_empty=$laradmin->navigation->isEmptyTags($right_menu_tag);
}
if($is_empty){
    return;
}
            

//// Remove border-bottom on major navs
$laradmin->assetManager->registerBodyClass('main-nav-no-border-bottom') ;
$laradmin->assetManager->registerBodyClass('has-minor-nav') ;
@endphp
<nav id="site-top-minor-nav" class="navbar navbar-default minor-nav @if(isset($scheme) and $scheme) minor-nav-{{$scheme}} @else minor-nav-subtle @endif {{$class??''}}">
    <div class="@if(!isset($with_container) or $with_container)  {{$laradmin->assetManager->isContainerFluid('container-fluid','container')}} @endif">
        <div class="navbar-header">

            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#minor-nav--collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="iconify" data-icon="entypo-chevron-thin-down" data-inline="false"></span>
                <noscript>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </noscript> 
            </button>

            <!-- Branding -->
            @if(isset($title) and $title)
                <a class=" navbar-brand " name="minor-navigation" role="presentation">
                    <small> {{$title}}</small>
                </a>
            @endif
            
            
            
        </div>

        <div class="collapse navbar-collapse" id="minor-nav--collapse">
            {{-- <p class="navbar-text">In this section</p> --}}
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav navbar-left">
                @if($root_tag)
                    @include('laradmin::menu', ['tag' => $root_tag,'class'=>'','with_icon'=>false]){{-- Note sending empty class prevent menu inheriting class var sent to the current file--}}
                @endif

                @if(isset($left_menu_tag))
                    @include('laradmin::menu', ['tag' => $left_menu_tag,'class'=>'','with_icon'=>false]){{-- Note sending empty class prevent menu inheriting class var sent to the current file--}}
                @endif
            </ul>

            
            @if(isset($right_menu_tag))
            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                    @include('laradmin::menu', ['tag' => $right_menu_tag,'class'=>'','with_icon'=>false]){{-- Note sending empty class prevent menu inheriting class var sent to the current file--}}
            </ul>
            @endif
        </div>
    </div>
</nav>
