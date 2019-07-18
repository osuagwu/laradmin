
  {{--
   Display menu items
   INPUTS:
   $item: [Menu] A decendante of Menu.
   $level: [Integer] The menu level, where the parent is 0 and children have higher number 
   $activate :[Boolean] has to be true for the active state of the link to be printed
   $class: [String optional] a class that should placed on the <li> tags
   $with_icon=true [Boolean optional] When false, Icons are not printed.
--}} 

  <li role="{{$item->ariaRole}}" class="menu-item-tag-{{$item->getTag()}} menu-item level-{{$level}} {{$item->cssClass }} {{$class ?? ''}}{{--TODO: Add role ARIA since this is now a pops menu on mouse over. But I dont know which role to give it. You might also need to let ARIA know when the popup is open and when it is closed??--}}
    @if($item->active and $activate) active @endif 
    @if($item->hasChildren()) 
       dropdown has-children
      @if($level>0) dropdown-submenu @endif {{--SXXCTHC: dropdown-submenu class allows for multi-level menu that bootstrap 3 does not provide. --}}
    @endif"  >
    @if($item->hasLink()) 
      <a {!! $item->getHtmlAttributes() !!} class=""
          {{--@if($item->hasChildren()) class="dropdown-toggle disabled" role="button" data-toggle="dropdown"  aria-haspopup="true" aria-expanded="false" @else class="" @endif  --}}
          href="{{$item->getFullLink() }}">
          
          {!!$item->htmlBefore!!}

          @if((!isset($with_icon) or $with_icon) and $item->hasIcon())
            @if($item->iconImage)
               <img aria-hidden="true" src="{{$item->iconImage}}" alt="-" style="width: 20px;" >
            @else 
               <i class="{{$item->iconClass}}"> </i>
            @endif
          @endif

          {{ $item->name }}
          @if($item->isExternalLink())<small title="Opens external webpage" class="external-link"><i class="fas fa-external-link-alt"></i></small>@endif
          @if($item->hasChildren() and $level==0) {{--Note that the reason we are printint the caret/chevron only at level==0 is because higher level caret are provided by another css--}} 
            {{-- <span class="custom-caret">
              <span class="iconify" data-icon="entypo-chevron-thin-down" data-inline="false"></span>
            </span>
            <noscript><span class="caret"></span></noscript>  --}}
          @endif 
          
          {!!$item->htmlAfter!!}
      </a>
        {{--Start caret for dropdown--}}
        @if($item->hasChildren() and $level==0) {{--SXXCTHC: Note that the reason we are printint the caret/chevron only at level==0 is because higher level caret are provided by another css--}} 
          <div class="dropdown-toggle custom-dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
            <span class="custom-caret">
              <span class="iconify" data-icon="entypo-chevron-thin-down" data-inline="false"></span>
            </span>
            <noscript><span class="caret"></span></noscript> 
          </div>
        @endif 
        {{--End caret for dropdown--}}
    @else
      <span class="navbar-text">{{-- TODO: if required also print things like iconImage, htmlBefore/After--}}
        {!!$item->htmlBefore!!}

        @if($item->hasIcon())
          @if($item->iconImage)
              <img aria-hidden="true" src="{{$item->iconImage}}" alt="-" style="width: 20px;" >
          @else 
              <i class="{{$item->iconClass}}"> </i>
          @endif
        @endif
        {{ $item->name}}
        {!!$item->htmlAfter!!}
      </span>
    @endif
    @if($item->hasChildren())
      <ul class="dropdown-menu " >
        @foreach($item->getChildren() as $child)
          {{--Do not show hidden menu--}}
          @if(!$item->isHidden())
            @if (Auth::guest() and $item->isHidden('guest'))
              @continue
            @elseif(!Auth::guest() and $item->isHidden('auth'))
              @continue
            @endif
          @else
            @continue;
          @endif


          {{--Do not show dummies--}}
          @continue($item->isDummy)
            

          @include('laradmin::menu.menu_item',['item'=>$child,'level'=>$level+1])

        @endforeach
      </ul>
    @endif
  </li>


  @if($item->js)
    @push('footer-scripts') <script>{!!$item->js!!}</script>  @endpush
  @endif
  
