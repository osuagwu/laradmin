<!DOCTYPE html> 
<html lang="{{ app()->getLocale() }}">
    @include('laradmin::inc.asset_manager.asset')
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="{{$metas['description']?? env('APP_DESCRIPTION','Laradmin')}}" >
    <meta name="robots" content="{{$metas['robots'] ?? 'all'}}">
    @if(isset($metas['google-site-verification']))
        <meta name="google-site-verification" content="{{$metas['google-site-verification']}}" />
    @endif
    {{--Meta stack--}} 
    @stack('meta')

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Webferendum').' | ' }}{{$pageTitle ?? ' '}}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- External styles-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
    
    
    <!--Head Styles--> 
    @stack('head-styles')
</head>
<body class="front-end user {{$laradmin->assetManager->getBodyClassesString()}}">
    {{--import facebook sdk--}}
    @include('laradmin::user.partials.social.facebook_js_sdk')
    <div id="app">
        <div id="site-top-and-content">
            <nav class="navbar navbar-default navbar-static-top main-nav">
                <div class="container{{$laradmin->assetManager->isContainerFluid('-fluid')}}">
                    <div class="navbar-header">

                        <!-- Collapsed Hamburger -->
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                            <span class="sr-only">Toggle Navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>

                        <!---Sidebar control------->
                        @stack('sidebar-control')

                        <!-- Branding Image -->
                        <a class=" navbar-brand " href="{{ url('/') }}">
                            @if(str_contains($laradmin->assetManager->getHeroType(),'super'))  {{--print the special logo for hero --}}
                            <div class="logo-hero-super visible-md visible-lg">{{-- The visibility class here is not required as this has already been done in the css file--}}
                                <img class="logo " src="/img/logo-hero-super.svg" alt="{{ config('app.name', 'Laradmin')}}" />
                            </div>
                            @endif
                            
                            <div class="logo-default @if(str_contains($laradmin->assetManager->getHeroType(),'super')) visible-sm visible-xs @endif">{{--if this is hero page, make the normal logo to appear only for small smaller screens as they do not show the hero--}}{{-- NOTE:The visibility class here is not required as this has already been done in the css file--}}
                                <img class="logo " src="/img/logo{{$laradmin->assetManager->getLogoType('-')}}.svg" alt="{{ config('app.name', 'Laradmin')}}" />
                            </div>
                        </a>
                        @if($laradmin->contentManager->hasSubAppName()) 
                        
                        <a  class="navbar-brand" href="{{$laradmin->contentManager->getSubAppUrl('/')}}">
                            <span class="sub-app-brand">
                                <span class="sub-app-name">{{$laradmin->contentManager->getSubAppName()}}</span>
                            </span>
                        </a>
                        
                        @endif
                    </div>

                    <div class="collapse navbar-collapse" id="app-navbar-collapse">
                        <!-- Left Side Of Navbar -->
                        
                        <ul class="nav navbar-nav">
                            {{--@include('laradmin::user.partials.plugins_menu')--}}
                            
                            
                            @include('laradmin::menu', ['tag' => 'primary'])
                            
                        </ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="nav navbar-nav navbar-right">
                            

                            <!-- Authentication Links -->
                            @if (Auth::guest())
                                <li class="guest-login-menu-item"><a class="guest-nav-link" href="{{ route('login') }}">Login</a></li>
                                <li class="guest-register-menu-item"><a class="guest-nav-link" href="{{ route('register') }}">Register</a></li>
                            @else
                                @include('laradmin::inc.user_alerts_badge')
                                
                                <li class="dropdown">
                                    @include('laradmin::inc.message_nav_item')
                                </li>
                                <li><a href="{{ route('user-notification-index') }}" class="bubble-nav-link"> @include('laradmin::inc.notifications_badge')</a></li>
                                
                                <li class="dropdown login-menu-item ">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                            <i class="far fa-user"></i> {{ str_limit(Auth::user()->name,6,'...') }} 
                                            <span class="iconify" data-icon="entypo-chevron-thin-down" data-inline="false"></span>
                                            <noscript><span class="caret"></span></noscript> 
                                    </a>

                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="{{route('user-home')}}">Dashboard</a></li>
                                        <li><a href="{{route('user-settings')}}">Settings</a></li>
                                        @can('cp') 
                                            <li><a href="{{route('cp')}}">Control panel</a></li>
                                        @endcan
                                        <li>
                                            <a href="{{ route('logout') }}"
                                                onclick="event.preventDefault();
                                                        document.getElementById('logout-form').submit();">
                                                Logout
                                            </a>

                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                {{ csrf_field() }}
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="site-content">
                @yield('content')
            </div>
        </div>
        @include('layouts/footer')
    </div>

    {{-- Scripts --}}
    <script src="{{asset('js/manifest.js')}}"></script>
    <script src="{{asset('js/vendor.js')}}"></script>
    <script src="{{asset('js/app.js')}}"></script>

    {{--For after loading libraries [good for loading vue components]--}}
    @stack('footer-scripts-after-library')

    {{--iconify.design--}}
    <script src="https://code.iconify.design/1/1.0.0-rc7/iconify.min.js"></script>

    {{--laradmin--}}
    {{--site--}}
    <script src="{{asset('vendor/laradmin/js/gen.js')}}"></script>
    <script src="{{asset('vendor/laradmin/user/js/user-plain.js')}}"></script>
    @stack('footer-scripts')

    <!--Twitter Scripts-->
    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
</body>
</html>
