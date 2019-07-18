{{--
    Renders source set array. Nothing will be printed if the srcset is empty

    $srcset array The sourceset array of array.Each item of the array  must have 'url' and 'width' indexes. The first item of the array should refer to the original image
    $alt string [optional only when $attrs is set] string The alt param of the image tag
    $class string [optional, and ignored $attrs is set] A CSS class(es) for the image tag.
    $sizes array The sizes array (default =33vw). Examples: ['(max-width: 320px) 280px',
                                                                '(max-width: 480px) 440px', 
                                                                '800px']
    $attrs array Array where eachc item is a key=>val pair and the key is is img tag attribut and the value is the val. Must not have 'src','srcset' or 'sizes' attributes else there'll be duplicates.
    --}}
@if(count($srcset))
<img @if(!isset($attrs))alt="{{$alt}}"  class="{{$class??''}}" @endif src="{{$srcset[0]['url']}}" 
    srcset="@foreach($srcset as $src){{$src['url'].' '.$src['width'].'w'}}@if(!$loop->last) , @endif @endforeach" 
    sizes="@if(isset($sizes))@foreach($sizes as $size) {{$size}} @if(!$loop->last) , @endif @endforeach @else 33vw @endif"
     @if(isset($attrs))@foreach($attrs as $attr=>$val) {{$attr}}="{{$val}}"  @endforeach @endif
>
@endif