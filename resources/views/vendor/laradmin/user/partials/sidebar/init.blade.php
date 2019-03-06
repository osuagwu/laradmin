{{-- Initialises sidebar, making @stack('sidebarcontrol'), etc available
    INPUT:
    $print_control=true Boolean When true(Default) side bar control is also printed
--}}
@php
    \BethelChika\Laradmin\Content\ContentManager::sidebar();
@endphp
@push('sidebar-control')
<div class="sidebar-control " title="Toggle sidebar">
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

{{--Also print the control here?--}}
@if(!isset($print_control) or $print_control)
    @stack('sidebar-control')
@endif

