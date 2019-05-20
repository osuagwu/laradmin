{{-- Use to make a section in content part of a page
    INPUT:
    type: {default(default),info,success,primary,danger,warning}
    title:  Optional section title
    legend:  Optional section legend
    body: Optional section body
    isFirst: [boolean] set to true if section is first one on page and you want it to offset the margin bottom of the main nav 
    isFullHeight: [boolean] Set to true you want section to conver the entire viewport.  
    slot: Any content inside the opening and closing of @component diretive
    class: Optional extra class or space separated classes for the section element

    USAGE:
    @component('laradmin::blade_components.section',['type'=>'info','body'=>'body','title'=>'Title','legend'=>'legend'])
    @endcomponent

    @component('laradmin::blade_components.section',['type'=>'info','title'=>'Title','legend'=>'legend'])
    slot
    @endcomponent

    @component('laradmin::blade_components.section',['type'=>'info','title'=>'Title','legend'=>'legend','isFirst'=>true,'isFullHeight'=>true])
    slot
    @endcomponent
--}}
<section class="section section-{{$type??' default '}} @if(isset($isFirst) and $isFirst==true) section-first @endif  @if(isset($isFullHeight) and $isFullHeight==true)section-full-height @endif {{$class??''}}" @if(isset($role)) role="{{$role}}" @endif>
    <div class="container">
        @if(isset($title) or isset($legend))
            <div class="title-box">
                <div class="row">
                    <div class="col-sm-12">
                        @if(isset($title))
                            <h1 class="heading-1 content-title">{{$title}}</h1>
                        @endif
                        @if(isset($legend))
                            <div class="title-legend faded">{{$legend}}</div>
                        @endif
                        
                    </div>
                    
                </div>
            </div>
        @endif
        @if(isset($body))
            {{$body}}
        @endif
        {{$slot}}
    </div>
</section>

