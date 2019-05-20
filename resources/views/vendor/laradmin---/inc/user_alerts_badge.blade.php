

    
    @if (!Auth::guest())
         
            
            
            
                @php
                    $nalerts=count(Auth::user()->getAlerts());
                    $nalerts=$nalerts?$nalerts:'';
                @endphp
                
                @if($nalerts)
                <li>
                    <a href="{{route('user-alerts')}}" class="bubble-nav-link @if($nalerts) call-to-action  @endif"> 
                    <span class="bubble-badge" >
                        <i class="far fa-surprise" title="Alerts"></i>
                        <span class="label label-danger bubble unseen-count" >
                            {{$nalerts}}
                        </span> 
                    </span>
                    </a>
                </li>
                @endif
        
            
      
    @endif
    

