@php
    $unreadMessagesCount=count(Auth::user()->unReadUserMessages());
@endphp

@if (!Auth::guest())
<li>
    <a href="{{route('user-message-index')}}" class="bubble-nav-link @if($unreadMessagesCount) call-to-action  @endif" >
        <span class="bubble-badge" >
            <i class="far fa-envelope" aria-hidden="true"></i> 
             
            
            
            @if($unreadMessagesCount)
                <span class="label label-danger bubble unseen-count  " >
                    {{$unreadMessagesCount}}
                </span> 
            @endif
               
            
        </span>    
   
    </a>
</li>
@endif
