
{{--
Print the recent posts listings

INPUTS:
$posts Collection <Corcel\Model\Page | BethelChika\Laradmin\WP\Models\Page> 
--}}
<div class="posts-listing with-large-thumb ">
    <div class="row">
    @foreach($posts as $post)  
        @if(!$post->image) @continue @endif
            <div class="col-sm-6 col-md-4">
                <div class="post">
                    <div class="thumbnail">
                        <a href="{{route('post',$post->post_name)}}" ><img src="{{$post->image}}" alt="{{$post->title}}"></a>
                        <div class="caption">
                            <h3><a href="{{route('post',$post->post_name)}}" >{{$post->title}}</a></h3>
                            <p>{{$post->post_excerpt}}</p>
                            
                        </div>
                    </div>
                </div>
            </div>
     
        
    @endforeach
    </div>
    
</div>