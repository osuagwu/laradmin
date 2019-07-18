@if ($errors->any())
    <div class="alert alert-danger alert-dismissable fade in padding-top-x3">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <h4>We have some errors</h4>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif




@foreach (['danger', 'warning', 'success', 'info'] as $msg)
    @if(Session::has($msg))
        <div class="alert alert-{{ $msg }} alert-dismissable fade in padding-top-x3">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            @if(!strcmp($msg,'success'))<h4><i class="fas fa-check"> </i> That is nice; it actually worked</h4>@endif  
            @if(!strcmp($msg,'danger'))<h4><i class="fas fa-times"> </i> Something went wrong</h4>@endif
            @if(!strcmp($msg,'warning'))<h4><i class="fas fa-exclamation-triangle"> </i> Something is not right</h4>@endif
            @if(!strcmp($msg,'info'))<h4><i class="fas fa-info-circle"> </i> We have information</h4>@endif
            @if(is_array(session($msg)))
                <ul class="">
                @foreach(session($msg) as $msg_i )
                    <li>{{ $msg_i }}</li>
                @endforeach
                </ul>
            @else    
            {{ session($msg) }}
            @endif
        </div>
    @endif
@endforeach


{{--
For User Auto user reactivation after a user has in the past deactivated oneself. When the user logs back in 
this displays automatic reactivation outcome message
--}}
@if(isset($autoUserReactivation))
    <div class="alert alert-{{$autoUserReactivation[0]?'info':'warning'}} alert-dismissable fade in padding-top-x3">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ $autoUserReactivation[1] }}
    </div>
@endif
