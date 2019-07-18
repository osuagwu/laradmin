@if (!Auth::guest())
    <a href="{{route('cp-user-message-index')}}" class="" aria-expanded="false">
        <span class=" bubble-badge">
            <i class="far fa-envelope" aria-hidden="true"></i>
            @php 
                $unreadMessagesCount=Auth::user()->getSystemUser()->unReadUserMessages()->count();
            @endphp
            
            @if($unreadMessagesCount)
                <span class="label label-danger bubble unseen-count" >
                    {{$unreadMessagesCount}}
                </span> 
            @endif
            
        </span>    
           
    
        
            
        
    </a>
    
    
@endif
