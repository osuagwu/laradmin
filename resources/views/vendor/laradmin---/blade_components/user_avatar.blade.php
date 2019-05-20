<div class="avatar-box avatar-horizontal clearfix ">
    @if($user->avatar)
        <img class="img-circle pull-left" src="{{$user->avatar}}" />
        
    @else
        
        <div class="avatar {{$class??'avatar-default'}} pull-left">
            <span class="avatar-text"> {{ucfirst(substr($user->name,0,1))}}</span>
        </div>
        
        
    @endif

    @if(isset($legend))
        @if(isset($sublegend))
            <div class="avatar-legend with-sub-legend">{{$legend}} <span class="sub-legend">{{$sublegend}}</span></div>
        @else(isset($legend))
            <div class="avatar-legend">{{$legend}}</div>
        @endif
    @endif
</div>