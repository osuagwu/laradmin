
    {{--
        Prints an icon representation for a user
    
        //Params
        @param User $user User object
        @param string $size The size e.g:{xxl,xl,lg,md,sm,xs}
        
    --}}
    <div class="user-icon user-icon-{{$size??'original-size'}} @if(!$user->avatar) user-icon-default @endif">
        @if($user->avatar)
            <img class="user-icon-img" src="{{$user->avatar}}" /> 
        @else  
            
            <span class="user-icon-text"> {{ucfirst(substr($user->name,0,1))}}</span>
            
        @endif

        @if(isset($slot) and strlen($slot))<div class="user-icon-content" >{{$slot}}</div>@endif
    </div>