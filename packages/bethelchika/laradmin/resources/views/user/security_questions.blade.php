@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.content_manager.stacks')
@section('content')
<section class="section section-primary section-first">
    <div class="container-fluid">
        @include('laradmin::user.partials.minor_nav',['left_menu_tag'=>'user_settings','scheme'=>'primary','title'=>'User settings','root_tag'=>false])
    </div>
</section>

<section class="section section-subtle section-light-bg section-diffuse section-diffuse-no-shadow">
    <div class="container-fluid">
        <div class="sidebar-mainbar">
            {{-- sidebar control --}}
            @include('laradmin::user.partials.sidebar.init') 
            <aside class="sidebar"  role="presentation">
                
                <div class="sidebar-content mCustomScrollbar" data-mcs-theme="minimal-dark">
                    <div class="sidebar-close-btn" title="Close sidebar">X</div>
                    <div class="back-to-usettings"><a href="{{route('user-settings')}}" title="Back to main settings" ><i class="fas fa-chevron-circle-left"></i> Back to main settings</a></div>
                    {{-- sidebar content --}}
                    @include('laradmin::user.partials.quick_settings')
                    
                </div>
            </aside>
    
            <!-- Page Content Holder -->
            <div class="mainbar" role="main">
                <div class="row">
                    <div class="col-md-12">  
                        @include('laradmin::menu.breadcrumb')  
                        

                        <div class="heading-huge">Manage your account access and security settings</div>
                        @include ('laradmin::inc.msg_board')
                        @include('laradmin::inc.email_confirmation_prompt')
                        <nav class="nav nav-tabs nav-flat">
                                @include('laradmin::menu',['tag'=>$laradmin->navigation->getMinorNavTag()])
                        </nav>

                        

                        <h3  class="heading-3" id="S-password">Security questions</h3>
                        @if(count($security_answers)<$security_answers_count)
                        <div class="row row-c no-elevation">
                            
                            <div class="col-xs-6 col-md-3 text-right"><strong>Missing security questions</strong> </div>
                            <div class="col-xs-6 col-md-9 "> 
                                <span class="fainted-03"> Please set security questions</span>  <a class="fainted-04" href="{{route('user-security-questions-edit')}}" title="Set security questions"> <i class="fas fa-pen"></i></a>
                                
                            </div>
                            
                        </div>
                        @else
                        @foreach($security_answers as $sa)
                        <div class="row row-c no-elevation">
                            
                            <div class="col-xs-6 col-md-3 text-right"><strong>{{$sa->securityQuestion->question}}</strong> </div>
                            <div class="col-xs-6 col-md-9 "> 
                                <span class="fainted-03">{{'*******'}}</span>  
                                <a tabindex="0" class="text-info"  role="button" data-toggle="popover" data-trigger="focus" title="Reminder" data-content="{{$sa->reminder?'Your reminder is, "'.$sa->reminder.'".':'You did not set a reminder.'}} Your answer is hidden for security reasons. "><i class="far fa-question-circle"></i></a>
                                <a class="fainted-04" href="{{route('user-security-questions-edit')}}" title="Edit security questions"> <i class="fas fa-pen"></i></a>
                            
                            </div>
                            
                        </div>
                        @endforeach
                        @endif
                        <div class="text-right"><a class="btn btn-primary btn-sm" href="{{route('user-security-questions-edit')}}" title="Edit security questions"> <i class="fas fa-user-edit"></i> Edit security questions</a></div>


                       
                        
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
