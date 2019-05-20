{{--
@inject('laradmin','laradmin')
--}}
@php
    \BethelChika\Laradmin\Content\ContentManager::sidebar();
@endphp
@push('sidebar-control')
<div class="sidebar-control pull-left" title="Toggle sidebar">
    <div class="sidebar-collapse-toggle">
        <button type="button" class="toggle-btn toggle-btn-default ">
            <span class="sr-only">Toggle Navigation</span> 
            <span class="icon-bar"></span> 
            <span class="icon-bar"></span> 
            <span class="icon-bar"></span>
            <span class="icon-bar-info hidden-xs" style="display:none">Sidebar</span>
        </button> 
    </div>
</div>
@endpush

{{--Also print the control here--}}
<div class="sidebar-inside-top-control">
    @stack('sidebar-control')
</div>

