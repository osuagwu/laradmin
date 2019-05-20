  {{--
   Display a navigation item
   INPUTS:
   $tag: [String] item tag/s.
   $activate: [Boolean, optional] When True(Default) marks the item that matches the current url as active
   $class: [String, optional] a class that should be placed on the <li> tags
   $layout: [String, optional] 'horizontal'(default) or 'vertical' menu
   $with_icon=true [Boolean optional] When false, Icons are not printed.

--}} 
@php
  $nav=app('laradmin')->navigation;
  if(!isset($activate)){
    $activate=true;
  }
  
  $menu=$nav->getMenuByTags($tag);//Note that menu can be also a menu item.
  if(!$menu ) {return;}
  if($activate){$nav->activates($tag);}

  if(!isset($class)){
    $class='';
  }

  if(!isset($layout)){
    $layout='horizontal';
  }

  if(!isset($with_icon)){
    $with_icon=true;
  }
@endphp
{{$menu->htmlBefore}}

@php
$items=[];
if($menu->hasChildren()){
  $items=$menu->getChildren();
}elseif($menu->isMenuItem()){
  $items=[$menu];
}
@endphp
@foreach($items as $item)

  {{--Do not show hidden menu TODO:(see ref:TODO-ISHIDEN-ANCESTORS in NavigationItem) SHould we also hide the menu if the parent is hidden, might be tricky b/c how far back do you want to go; perhaps the isHidden() should check ancestors and hide if any is hidden--}}
  @if(!$item->isHidden())
    @if (Auth::guest() and $item->isHidden('guest'))
      @continue
    @elseif(!Auth::guest() and $item->isHidden('auth'))
      @continue
    @endif
  @else
    @continue
  @endif

  {{--Do not show dummies--}}
  @if($item->isMenuItem())
    @continue($item->isDummy)
  @endif

  @if($layout=='vertical')
    @include('laradmin::menu.menu_item_vertical',['item'=>$item,'class'=>$class,'level'=>0,'activate'=>$activate])
  @else
    {{--horizontal--}}
    @include('laradmin::menu.menu_item',['item'=>$item,'class'=>$class,'level'=>0,'activate'=>$activate])
  @endif
  
@endforeach

{{$menu->htmlAfter}}
@if($menu->js)
  @push('footer-scripts') <script>{{$menu->js}}</script>  @endpush
@endif