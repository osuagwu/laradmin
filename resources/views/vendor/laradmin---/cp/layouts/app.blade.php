<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Webferendum').' &#8213; ' }}{{$pageTitle??' '}}</title>

    <!-- External styles-->
    <!--Fontawesome-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">    
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    
    
    <!-- Admin specific Styles--> 
    <link href="{{ asset('vendor/laradmin/cp/css/admin.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/laradmin/cp/css/structure-plain.css') }}" rel="stylesheet">    
    <link href="{{ asset('vendor/laradmin/cp/css/admin-plain.css') }}" rel="stylesheet">
    
    

</head>
<body>
    <div id="app">
        <header role="banner"
            <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#sidebar-menu-toggle" id="sidebar-menu-toggle"><span class="glyphicon glyphicon-list text-info" aria-hidden="true"></span></a>
                    <a class="navbar-brand" href="{{ url('/')}}">{{config('app.name', 'Webferendum')}}</a>
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">

                        <!-- Left Side Of Navbar -->
                        <ul class="nav navbar-nav">
                            &nbsp;
                        </ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="nav navbar-nav navbar-right">
                            
                            <!-- Authentication Links -->
                            @if (Auth::guest())
                                <li><a href="{{ route('login') }}">Login</a></li>
                                <li><a href="{{ route('register') }}">Register</a></li>
                                
                            @else
                                <li><a href="#"><span class="glyphicon glyphicon-question-sign text-info" aria-hidden="true"></span> Help</a></li>
                                
                                <li class="">
                                    @include('laradmin::inc.cp_message_nav_item')
                                </li>

                                <li><a href="{{ route('cp-notification-index') }}"> @include('laradmin::inc.cp_notifications_badge')</a></li>
                            
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle " data-toggle="dropdown" role="button" aria-expanded="false">
                                        <span class="glyphicon glyphicon-user text-primary" aria-hidden="true"></span>
                                        {{ substr(Auth::user()->name,0,13) }} <span class="caret"></span>
                                    </a>

                                    <ul class="dropdown-menu" role="menu">
                                        <li>
                                            <a href="{{ route('user-profile') }}">
                                                <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                                                My profile
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('user-profile-edit',['user_settings','personal']) }}">
                                                <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                                                Edit details
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('logout') }}"
                                                onclick="event.preventDefault();
                                                            document.getElementById('logout-form').submit();">
                                                <span class="glyphicon glyphicon-off" aria-hidden="true"></span> Logout
                                            </a>

                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                {{ csrf_field() }}
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                            
                        </ul>
                        <form class="navbar-form navbar-right" action="#" method="GET">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search..." id="query" name="search" value="">
                                <div class="input-group-btn">
                                    <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-search"></span></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </nav>
        </header>

        <div id="wrapper" class="toggled">
        <div class="container-fluid">
            <!-- Sidebar -->
            <aside role="presentation">
                <div id="sidebar-wrapper">
                    <nav class="nav">
                        <ul class="sidebar-nav">
                            <li class="sidebar-brand">
                                <br>
                            </li>
                            <li class="sidebar-brand">
                                <a href="{{route('user-profile')}}" class="navbar-brand">
                                    @if(Auth::user()->avatar)
                                        <img class="img-circle " style="width:60px;height:auto;margin-left:20px;" src="{{Auth::user()->avatar}}" alt="{{Auth::user()->name}}" />
                                    @else
                                        <button  class="btn btn-circle btn-success btn-lg"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> </button>
                                        Profile
                                    @endif
                                    
                                </a>
                            </li>
                            <li>
                                <a href="{{route('cp')}}"><span class="glyphicon glyphicon-home" aria-hidden="true"></span> Control home</a>
                            </li>
                                            
                            <li>
                                <span class="text-primary"> USER MANAGEMENT</span>
                            </li>
                            <li>
                                <a href="{{route('cp-users')}}"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Users</a>
                            </li>
                            <li>
                                <a href="{{route('cp-user-groups')}}"><span class="glyphicon glyphicon-user" aria-hidden="true"></span><span class="glyphicon glyphicon-user" aria-hidden="true"></span> User groups</a>
                            </li>
                            <li>
                                <span class="text-primary"> SOURCES</span>
                            </li>
                            @include('laradmin::menu',['tag'=>'admin.sources','layout'=>'vertical'])
                            
                            <li>
                                <span class="text-primary">GENERAL </span>
                            </li>
                            @include('laradmin::menu',['tag'=>'admin.general'])
                            <li>
                                <span class="text-primary">MESSAGES AND NOTICES </span>
                            </li>
                            <li>
                            <a href="{{route('cp-user-message-index')}}">cMessage</a>
                            </li>
                            <li>
                            <a href="{{route('cp-notification-index')}}">cNotifications</a>
                            </li>
                            <li>
                                <span class="text-primary">PLUGINS </span>
                            </li>
                            <li>
                                <a href="{{'route(apps/reincarnate)'}}"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Reincarnate </a>
                            </li>

                            @include('laradmin::menu',['tag'=>'admin.apps'])
                        
                        </ul>
                    </nav>
                </div>
            <!-- /#sidebar-wrapper -->
            </aside>

            <!-- Page Content -->
            <div role="main" id="page-content-wrapper">
                <div class="container-fluid">
                    <div class="row page-content-top">
                        <div class="col-lg-12">
                            <br /><br />
                            @yield('page-top')
                        </div>
                    </div>
                    <div class="row page-content-body">
                        <div class="col-lg-12">
                            @include ('laradmin::inc.msg_board')
                            @include('laradmin::inc.email_confirmation_prompt')
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
            <!-- /#page-content-wrapper -->
        </div>
        </div>
        <!-- /#wrapper -->


    </div><!--#app-->











   {{-- 

    <!-- General site Scripts TODO: Consider determining which frameworks we need here and replacing these with there cdns-->
    <script src="{{asset('js/manifest.js')}}"></script>
    <script src="{{asset('js/vendor.js')}}"></script>
    <script src="{{asset('js/app.js')}}"></script>
    This only add libraries some of which we do not use for the admin page 
    
    </script>--}} 

    {{--jQuery--}}
    <script
    src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
    crossorigin="anonymous"></script> 

    {{-- bootstrap --}}
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    
    <script src="{{asset('vendor/laradmin/cp/js/admin.js')}}"></script>-->
    <script src="{{asset('vendor/laradmin/cp/js/structure-plain.js')}}"></script>
    <script src="{{asset('vendor/laradmin/cp/js/admin-plain.js')}}"></script>
    


    @stack('footer-scripts')
</body>
</html>
