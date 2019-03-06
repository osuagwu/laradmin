@if (!Auth::guest())
    <a href="{{route('user-message-index')}}" class="bubble-nav-link" >
        <span class="bubble-badge" >
            <i class="far fa-envelope" aria-hidden="true"></i> 
             
            @php
                $unreadMessagesCount=count(Auth::user()->unReadUserMessages());
            @endphp
            
            @if($unreadMessagesCount)
                <span class="label label-danger bubble unseen-count" >
                    {{$unreadMessagesCount}}
                </span> 
            @endif
               
            
        </span>    
   
    </a>
    
@endif
