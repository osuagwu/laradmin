
{{--
Print a blog posts 


INPUTS:
$posts Collection of Corcel\Model\Post | BethelChika\Laradmin\WP\Models\Post 
$summary int {0,1(default)} Prints summary of post when ==1/true
$class string The css class of each post wrapper. Default to empty string.
$box_class string The css class of the wrapper of the all the posts . Default to empty string.
$layout string {'horizontal','vertical'} If 'horizontal'=> posts will lay in one row else it will be in a colunm. So setting it to vertical is as good as setting it to anything or not providing it at all.
--}}

<div class="blog-posts {{$box_class??''}} @if(isset($layout)) @if(str_is($layout,'horizontal')) blog-posts-h0 @else blog-posts-v0 @endif @else blog-posts-v0 @endif">
    @foreach($posts as $post)
        
        @include('laradmin::user.wp.partials.blog_post',['post'=>$post, 'summary'=>$summary??1,'class'=>$class??''])
        
    @endforeach
</div>