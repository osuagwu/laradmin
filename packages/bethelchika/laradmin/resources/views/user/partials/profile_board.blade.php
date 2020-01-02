{{--
    Prints the information board for profile page

INPUT:
$laradmin Laradmin 
    --}}
<div class="row" role="banner">
    <div class="col-sm-6 col-md-6 ">  
        <div class="my-dash-card">
            <div class="row">
                <div class="col-xs-12 col-md-2">
                    <div class="first-col">
                        @push('head-styles')
                        <style >
                            .my-dash-card .first-col div.user-icon.user-icon-default{
                                background-color:{{\BethelChika\Laradmin\Tools\Color::colorLuminance($laradmin->assetManager->getbrands()['primary'],-0.25)}};
                                border:none;
                            }
                        </style>
                        @endpush
                        @component('laradmin::components.user_icon',['user'=>Auth::user(),'size'=>'lg'])
                        @endcomponent
                        <div class="text-right fainted-07">
                            <small ><a href="{{route('user-settings')}}" title="Profile picture"><i class="fas fa-camera"> </i> </a></small>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-10">
                    <div class="second-col">
                        <h1 class="title">{{Auth::user()->name}}</h1>
                        <div class="item">
                            <small class="fainted-07">#ID{{Auth::user()->id}} ({{Auth::user()->email}})</small>
                            <small><a class="text-white" href="{{route('user-profile')}}" title="Edit profile"> Change profile <i class="fas fa-pen"> </i> </a></small>
                            <small><a class="text-white" href="{{route('user-settings')}}" title="Account settings"> Account settings <i class="fas fa-pen"> </i> </a></small>
                            @can('cp') 
                                <small><a class="text-white" href="{{route('cp')}}" title="Control panel"> Control panel <i class="fas fa-cog"> </i> </a></small>
                            @endcan
                        </div>
                        <div class="item fainted-07">
                            <small><span>Last login</span> <i class="fas fa-clock"> </i> {{Auth::user()->last_login_at ? Auth::user()->last_login_at->diffForHumans():'Never'}}</small>
                        </div>
                        <div class="item fainted-07"><small><span>Registered since</span> <i class="fas fa-clock"> </i> {{Auth::user()->created_at->diffForHumans()}}</small></div>
                       
                    </div>
                </div>
            </div>
        </div>
        
    </div> 
    <div class="col-sm-6 col-md-2 ">
        <div class="stats-block-content">

            <div class="stats-item stats-item-primary">
                <div class="stats-content">
                    <h3 class="stats-title">Messages</h3> 
                    <div class="row stats-body">
                        <div class=" col-xs-6 left">
                            <i class="fa fa-envelope"></i>
                        </div>
                        <div class="col-xs-6 right text-right">
                            
                            {{count(Auth::user()->unReadUserMessages())}}
                        
                            <span class="legend">New</span>
                        </div>
                    </div>
                    <div class="row stats-footer">
                        <div class="right col-xs-6"><a href="{{route('user-message-index')}}">Open</a></div>
                        <div class="left col-xs-6 text-right"><a href="{{route('user-message-index')}}"><i class="fa fa-arrow-alt-circle-right"></i></a></div>
                    </div>
                </div>
            </div>
        
        </div>
    </div>
    <div class="col-sm-6 col-md-2 ">
        <div class="stats-block-content">
            <div class="stats-item stats-item-info">
                <div class="stats-content">
                    <h3 class="stats-title">Notifications</h3> 
                    <div class="row stats-body">
                        <div class=" col-xs-6 left">
                            <i class="fa fa-bell"></i>
                        </div>
                        <div class="col-xs-6 right text-right">
                                {{Auth::user()->unReadNotifications()->count()}}
                            <span class="legend">New</span>
                        </div>
                    </div>
                    <div class="row stats-footer">
                        <div class="right col-xs-6"><a href="{{route('user-notification-index')}}">Open</a></div>
                        <div class="left col-xs-6 text-right"><a href="{{route('user-notification-index')}}"><i class="fa fa-arrow-alt-circle-right"></i></a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-2 ">
        <div class="stats-block-content">
            <div class=" stats-item stats-item-danger">
                <div class="stats-content">
                    <h3 class="stats-title">Alerts</h3> 
                    <div class="row stats-body">
                        <div class=" col-xs-6 left">
                            <i class="fa fa-fire"></i>
                        </div>
                        <div class="col-xs-6 right text-right">
                            {{count(Auth::user()->getAlerts())}}
                            <span class="legend">Unresolved</span>
                        </div>
                    </div>
                    <div class="row stats-footer">
                        <div class="right col-xs-6"><a href="{{route('user-alerts')}}">View</a></div>
                        <div class="left col-xs-6 text-right"><a href="{{route('user-alerts')}}"><i class="fa fa-arrow-alt-circle-right"></i></a></div>
                    </div>
                </div>
            </div>
        </div>
    
    </div>
</div>