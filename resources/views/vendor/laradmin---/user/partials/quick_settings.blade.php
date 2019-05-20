
{{--  <h3 class="heading-3 ">Quick links</h3>  --}}
<nav>
        <ul class="nav nav-piils">
        @include('laradmin::menu',['tag'=>'user_settings.security'])
        <li><hr class="list-separator"></li>
        @include('laradmin::menu',['tag'=>'user_settings.account'])
        <li><hr class="list-separator"></li>
        @include('laradmin::menu',['tag'=>'user_settings.external_accounts'])
        <li><hr class="list-separator"></li>
        @include('laradmin::menu',['tag'=>'user_settings.account_control'])
        <li><hr class="list-separator"></li>
        <li role="presentation"><a href="{{route('user-message-index')}}"> <i class="far fa-envelope"></i> uMessage</a></li>
        <li role="presentation"><a href="{{route('user-notification-index')}}"> <i class="far fa-envelope"></i> Notification</a></li>
                <li role="presentation">
                        <a href="{{route('user-alerts')}}"> 
                        
                                <i class="far fa-surprise" title="Alerts"></i> Alerts
                        
                        </a>
                </li>
        </ul>
</nav>