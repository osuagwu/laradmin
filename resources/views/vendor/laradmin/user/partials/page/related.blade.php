
{{--
Print the immediate related pages of a page

INPUTS:
$page Corcel\Model\Page | BethelChika\Laradmin\WP\Models\Page The page  
$children Collection The collections of child pages. This can be empty collection incase there are no children.
$parent Corcel\Model\Page | BethelChika\Laradmin\WP\Models\Page The parent of the page. This can be null.
--}}
<div class="related ">
    <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading"> <h3 class="heading-3 panel-title">In this section</h3></div>
        <div class="panel-body">
            @if($parent)
                <h4 class="heading-5"><a class="parent" href="{{route('page',$parent->post_name)}}"><i class="fas fa-arrow-up"></i> {{$parent->title}}</h4></a>
            @endif
            
        </div>
        @if($children->count() )
        <!-- List group -->
        <div class="list-group">
            <a class="list-group-item parent active" href="{{route('page',$page->post_name)}}"><i class="fas fa-arrow-down"></i> {{$page->title}}</a>
            
            @foreach($children as $child)
            <a class="list-group-item" href="{{route('page',$child->post_name)}}">{{$child->title}}</a>
            @endforeach 
        </div>
        @endif
    </div>
    
</div>