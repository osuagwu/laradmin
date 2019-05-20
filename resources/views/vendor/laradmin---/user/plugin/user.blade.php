@extends('laradmin::user.layouts.app') 
 
@section('content')
<section class="section section-subtle" style="border-bottom:1px solid #ddd">
    @include('laradmin::user.partials.minor_nav',['scheme'=>'subtle','with_container'=>true,'with_icon'=>false,'left_menu_tag'=>'user_settings','root_tag'=>false])
</section>
<section class="section section-default">
        <div class="container-fluid">
            
                <div class="sidebar-mainbar">
                    {{-- sidebar control --}}
                    @include('laradmin::user.partials.sidebar.init') 
                    <aside class="sidebar" role="presentation">
                        
                        <div class="sidebar-content mCustomScrollbar" data-mcs-theme="minimal-dark">
                            {{-- sidebar content --}}
                            <div class="sidebar-close-btn" title="Close sidebar">X</div>
                            <div class="back-to-usettings"><a href="{{route('user-settings')}}" title="Back to main settings" ><i class="fas fa-chevron-circle-left"></i> Back to main settings</a></div>
                            @include('laradmin::user.partials.quick_settings')
                            
                        </div>
                    </aside>
            
                        <!-- Page Content Holder -->
                    <div class="mainbar" role="main">
                        <div class="plugin-user-content">
                            {!!$content!!}
                        </div>
                    </div>
                </div>
        </div>
</section>
  
@endsection
