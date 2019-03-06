{{----
    Prints Minor nav which shown be below the main nav. 
    This uses a bootstrap navbar class but without the container class by deafult to avoid mistakenly nesting containers when this partial in included in a view;

    INPUTS
    $left_menu_tag=page_family string  Tag or dot separated manu tag that goes to the left
    $right_menu_tag string Tag or dot separated manu tag that goes to the right
    $title='In this setion' string The title of the bar . You can pass in empty string to avoid the default string.
    $with_container boolean Set to false to remove bootstrap container inside the minor nav (Use when you already have container wrapping the output of this file).
    $scheme string The scheme to used for nav tag. e.g 'primary' which makes the minor nav a a primary look and feel
    $class string The class added to the nav tag
--}}
@php
//// Remove border-bottom on major navs
$laradmin->assetManager->registerBodyClass('main-nav-no-border-bottom') 
@endphp
<nav id="site-top-minor-nav" class="navbar navbar-default minor-nav @if(isset($scheme)) minor-nav-{{$scheme}} @else minor-nav-subtle @endif {{$class??''}}">
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
            @if(isset($title) and !$title)
            @else
                <a class=" navbar-brand " name="minor-navigation" role="presentation">
                    <small> {{$title??'In this section'}}</small>
                </a>
            @endif
            
            
            
            
            
        </div>

        <div class="collapse navbar-collapse" id="minor-nav--collapse">
            {{-- <p class="navbar-text">In this section</p> --}}
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav navbar-left">
                @include('laradmin::menu', ['tag' => $left_menu_tag??'page_family','class'=>'','with_icon'=>false]){{-- Note sending empty class prevent menu inheriting class var sent to the current file--}}
                
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