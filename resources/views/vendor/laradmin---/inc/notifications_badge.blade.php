@if (!Auth::guest())
    @php
        $nNotices=Auth::user()->unReadNotifications()->count();
        $nNotices=$nNotices?$nNotices:'';
    @endphp
<li><a href="{{ route('user-notification-index') }}" class="bubble-nav-link @if($nNotices) call-to-action @endif">
    <span >  
        
        <span class="bubble-badge" >
        <i class="far fa-bell" title="Notifications"></i>
        
            
            
            @if($nNotices)
                <span class="label label-danger bubble unseen-count" >
                    {{$nNotices}}
                </span> 
            @endif
       
        </span>
    </span> 
</a></li>
@endif
