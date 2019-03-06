
{{--  <h3 class="heading-3 ">Quick links</h3>  --}}
<nav>
        <ul class="nav nav-piils">
        @include('laradmin::menu',['tag'=>'user_settings.security'])
        <hr class="list-separator">
        @include('laradmin::menu',['tag'=>'user_settings.account'])
        <hr class="list-separator">
        @include('laradmin::menu',['tag'=>'user_settings.external_accounts'])
        <hr class="list-separator">
        @include('laradmin::menu',['tag'=>'user_settings.account_control'])
        <hr class="list-separator">
        <li role="presentation"><a href="{{route('user-message-index')}}"> <i class="far fa-envelope"></i> uMessage</a></li>
        <li role="presentation"><a href="{{route('user-notification-index')}}"> <i class="far fa-envelope"></i> Notification</a></li>
                <li role="presentation">
                        <a href="{{route('user-alerts')}}"> 
                        
                                <i class="far fa-surprise" title="Alerts"></i> Alerts
                        
                        </a>
                </li>
        </ul>
</nav>