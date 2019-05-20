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
            @if(!strcmp($msg,'success'))<i class="fas fa-check"></i>@endif  
            {{ session($msg) }}
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
