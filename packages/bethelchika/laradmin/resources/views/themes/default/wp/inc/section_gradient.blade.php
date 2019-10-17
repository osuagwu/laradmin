{{---
    /*
    * Checks if a page has css gredient settings and print class names corresponding to the settings.
    * The @PHP part is created to allow the css for the gradient to be generated on the fly 
    * since pre creating the it with css means a lot of rules are created which hugely 
    * increases the css file size.
    */

    [INPUTS]
    $post \BethelChika\WP\Models\Page | Corcel\Model\Page If given all the other inputs will be read from this object.
    OR
    $scheme string The first brand color name for the gradient. e.g {primary,info,danger,...}.
    $brand2 string The second brand name for the second color of the gradient. e.g ,, 
    $direction string[optional] Any valid CSS gradient direction. e.g. {left,right top,let bottom, top, left top(defualt) ...}
    $fainted integer[optional] The level (1-100) of opacity of the gradient.
    [Returns] CSS class names. And pushes CSS rules for the class names to the <head>
    --}}
@php
if(isset($post) and $post){
    if($post->meta->scheme){//If the page has got scheem set them we assume that it may as well set the rest.
        $scheme=$post->meta->scheme;
        $brand2=$post->meta->linear_gradient_brand2??null;
        $fainted=$post->meta->linear_gradient_fainted??null;
        $direction=$post->meta->linear_gradient_direction;
    }
}
if(!isset($brand2) or !$brand2){
    return '';
}
if(!isset($scheme) or !$scheme){
    return '';
}
if(!isset($direction) or !$direction){
    $direction='left top';
}

$direction=trim(preg_replace('/\s+/', ' ', $direction));//First make sure that there is no double space. Only one space between words is allowed. 
$directions=explode(' ',$direction);//and then explode.

$direction_opposite_map=['left'=>'right',
                    'right'=>'left',
                    'top'=>'bottom',
                    'bottom'=>'top'];


$direction_opposites=[];
foreach($directions as $dr){
    $direction_opposites[]=$direction_opposite_map[$dr];
}

$bands=$laradmin->assetManager->getbrands();
$color_start=$bands[$scheme];
$color_end=$bands[$brand2];

if(isset($fainted)){
    $color_start=\BethelChika\Laradmin\Tools\Color::colorBlendByOpacity($color_start, intval($fainted));
    $color_end=\BethelChika\Laradmin\Tools\Color::colorBlendByOpacity($color_end,intval($fainted));
}

// It is not absolutely neccessary but lets create a few color stops made from $color_start and $color_end.
$levels=[25,50,75];
if(!config('laradmin.regular_section_gradient',true)){
    $levels=array_reverse($levels);
}
foreach($levels as $level){
    $grads[]=\BethelChika\Laradmin\Tools\Color::colorBlendByOpacity($color_end, $level, $color_start);
}
@endphp
{{--
    Now we make outputs
--}}
@if(isset($brand2))section-linear-gradient-{{$brand2.'-'.implode('-',$directions)}} @if(isset($fainted))section-linear-gradient-fainted-0{{$fainted}}@endif @endif 
@push('head-styles')
<style>
.section-{{$scheme}}.section-linear-gradient-{{$brand2}}-{{implode('-',$directions)}}@if(isset($fainted)).section-linear-gradient-fainted-0{{$fainted}}@endif {
    background-image: -webkit-gradient(linear, {{implode(' ',$direction_opposites)}}, {{implode(' ', $directions)}}, from({{$color_start}}), color-stop(36%, {{$grads[0]}}), color-stop(67%, {{$grads[1]}}), color-stop(93%, {{$grads[2]}}), to({{$color_end}}));
    background-image: linear-gradient(to {{implode(' ', $directions)}}, {{$color_start}} 0%, {{$grads[0]}} 36%, {{$grads[1]}} 67%, {{$grads[2]}} 93%, {{$color_end}} 100%);
  }
</style>
@endpush