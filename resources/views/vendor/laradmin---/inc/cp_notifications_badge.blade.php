@if (!Auth::guest())
    <span >  
        
        <span class="bubble-badge" >
            <i class="far fa-bell" title="Notifications"></i>
            @php
                $nNotices=Auth::user()->getSystemUser()->unReadNotifications()->count();
                $nNotices=$nNotices?$nNotices:'';
            @endphp
            @if($nNotices)
                <span class="label label-danger bubble unseen-count" >
                    {{$nNotices}}
                </span> 
            @endif
       
        </span>
    </span>
    
@endif
