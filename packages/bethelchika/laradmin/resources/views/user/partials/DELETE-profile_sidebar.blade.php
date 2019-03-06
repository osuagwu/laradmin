

    
    <h3 class="heading-3 padding-top-x1 padding-bottom-x7"> Welcome to your account settings</h3>
    
    
        
        <!-- Sidebar Holder -->
        <nav >
            

            <ul class="list-unstyled user-profile-sidebar-menu">
                <li class="title @if(Route::is('user-security')){{'active'}}@endif">
                    <div class="flex-group">
                        <div ><a  class="title @if(Route::is('user-security')){{'active'}}@endif" href="{{route('user-security')}}"> Security </a></div>
                        
                        <span class="menu-collapse-icon" data-target="#settings-menu-security" data-toggle="collapse" aria-expanded="true"> </span>
                    </div>
                    <div class="collapse in vertical-nav" id="settings-menu-security">
                        <div class="vertical-nav-item"><a href="@if(!Route::is('user-security')){{route('user-security')}}@endif#S-password">Password</a></div> 
                    </div>
                </li>
                
                <li class="title @if(Route::is('user-profile')){{'active'}}@endif">
                    <div class="flex-group">
                        <div ><a  class="title @if(Route::is('user-profile')){{'active'}}@endif"  href="{{route('user-profile')}}"> Personal details</a></div>
                        
                        <span class="menu-collapse-icon" data-target="#settings-menu-personal-1" data-toggle="collapse" aria-expanded="true"> </span>
                    </div>
                    <div class="collapse in vertical-nav" id="settings-menu-personal-1">
                        <div class="vertical-nav-item"><a href="@if(!Route::is('user-profile')){{route('user-profile')}}@endif{{'#PD-personal-information'}}">Personal information</a></div>
                        <div class="vertical-nav-item"><a href="@if(!Route::is('user-profile')){{route('user-profile')}}@endif#PD-contact-details">Contails details</a></div>
                        <div class="vertical-nav-item"><a href="@if(!Route::is('user-profile')){{route('user-profile')}}@endif#PD-location">Location</a></div>
                    </div>
                </li>


                <li class="title @if(Route::is('social-user-external')){{'active'}}@endif">
                    <div class="flex-group">
                        <div ><a class="title  @if(Route::is('social-user-external')){{'active'}}@endif"  href="{{route('social-user-external')}}"> External accounts </a></div>
                        
                        <span class="menu-collapse-icon" data-target="#settings-menu-linked-accounts" data-toggle="collapse" aria-expanded="true"> </span>
                    </div>
                    <div class="collapse in vertical-nav" id="settings-menu-linked-accounts">
                        <div class="vertical-nav-item"><a href="@if(!Route::is('social-user-external')){{route('social-user-external')}}@endif#EA-social-user">Social user accounts</a></div>
                        <div class="vertical-nav-item"><a href="@if(!Route::is('social-user-external')){{route('social-user-external')}}@endif#EA-email">Emails</a></div>
                        
                    </div>
                </li>

                <li class="title @if(Route::is('user-account-control')){{'active'}}@endif">
                    <div class="flex-group">
                        <div ><a class="title @if(Route::is('user-account-control')){{'active'}}@endif"  href="{{route('user-account-control')}}"> Account control </a></div>
                        
                        <span class="menu-collapse-icon" data-target="#settings-menu-account-control" data-toggle="collapse" aria-expanded="true"> </span>
                    </div>
                    <div class="collapse in vertical-nav" id="settings-menu-account-control">
                        <div class="vertical-nav-item"><a href="@if(!Route::is('user-account-control')){{route('user-account-control')}}@endif#AC-deactivate">Deactivate temporarily</a></div>
                        <div class="vertical-nav-item"><a href="@if(!Route::is('user-account-control')){{route('user-account-control')}}@endif#AC-delete">Delete account permanently</a></div>
                        
                    </div>
                </li>
                
                
                <li class="title @if(Route::is('user-plugin-settings')){{'active'}}@endif">
                    <a class="title @if(Route::is('user-plugin-settings')){{'active'}}@endif" href="{{route('user-plugin-settings')}}">Application Settings</a>
                    
                    
                </li>
            </ul>

            <ul>
                @include('laradmin::menu', ['tag' => 'app_settings'])

            </ul>

            
        </nav>
    
    @push('footer-scripts')
    <script>
        $(document).ready(function(){
            // Collaps all user profile settings setting menu in the sidebar except for the active one
            $(".user-profile-sidebar-menu li .collapse").removeClass('in');
            $(".user-profile-sidebar-menu li .menu-collapse-icon").attr("aria-expanded","false");

            $(".user-profile-sidebar-menu li.active .collapse").addClass('in');
            $(".user-profile-sidebar-menu li.active .menu-collapse-icon").attr("aria-expanded","true");

        
        });
    </script>
    @endpush


    