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

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{--Meta stack--}}
    @stack('meta')
    
    <title>
        @isset($pageTitle) {{ config('app.name', 'Laradmin').' | '.$pageTitle }} 
        @else {{config('app.name', 'Laradmin')}} 
        @endisset
    </title>

    <!-- Styles -->
    <link href="{{ asset('vendor/laradmin/user/css/user.css') }}" rel="stylesheet">

    <!-- External styles-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">    

    <!--mCustomScrollbar-->
    <link rel="stylesheet" href="//malihu.github.io/custom-scrollbar/jquery.mCustomScrollbar.min.css" />
    {{--TODO: include the image for mCustomScrollbar --}}
    
    <!--Head Styles-->
    @stack('head-styles')

</head>
<body class="front-end user {{$laradmin->assetManager->getBodyClassesString()}}">

    {{--import facebook sdk--}}
    @include('laradmin::user.partials.social.facebook_js_sdk')

    <div id="app">
        <div id="site-top-and-content">
            <header role="banner">
                <nav id="site-top" class="navbar navbar-default navbar-static-top main-nav">
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
                            {{--@stack('sidebar-control') Disabling for now b/c it seem it doesn't make site look good may be--}}

                            <!-- Branding Image -->
                            <a class=" navbar-brand " href="{{ url('/') }}">
                                <div class="">
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
                            <ul class="nav navbar-nav navbar-left">
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
                                    
                                    @include('laradmin::inc.message_nav_item')
                                    
                                    @include('laradmin::inc.notifications_badge')
                                    
                                    <li class="dropdown login-menu-item ">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                                <i class="far fa-user"></i> {{ str_limit(Auth::user()->name,6,'...') }} 
                                                <span class="custom-caret">
                                                    <span class="iconify" data-icon="entypo-chevron-thin-down" data-inline="false"></span>
                                                </span>
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
            </header>
            
            <div class="site-content">
                
                @yield('content')
            </div>
        </div>

        @include('laradmin::user/layouts/footer')

    </div>  <!--app-->    
    
    {{-- Scripts 
    <script src="{{asset('js/manifest.js')}}"></script>
    <script src="{{asset('js/vendor.js')}}"></script>
    <script src="{{asset('js/app.js')}}"></script>
    <script src="{{asset('user/js/user.js')}}"></script>--}}


    {{--jQuery--}}
    <script
    src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
    crossorigin="anonymous"></script> 

    {{-- bootstrap --}}
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>


    {{--Vue--}}
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    
    {{--For after loading libraries [good for loading vue components]--}}
    @stack('footer-scripts-after-library')

    {{--iconify.design--}}
    <script src="https://code.iconify.design/1/1.0.0-rc7/iconify.min.js"></script>
    

    {{--site--}}
    <script src="{{asset('vendor/laradmin/js/gen.js')}}"></script>
    <script src="{{asset('vendor/laradmin/user/js/user-plain.js')}}"></script>
    @stack('footer-scripts')

    <!--Twitter Scripts-->
    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

    <!--mCustomScrollbar-->
    <script src="//malihu.github.io/custom-scrollbar/jquery.mCustomScrollbar.concat.min.js" ></script>
</body>
</html>
