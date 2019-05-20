
{{--
Print a blog post 
The output of the file should be wrapped with: 
'<div class="blog-posts blog-posts-h0">' for horizontal or 
'<div class="blog-posts blog-posts-v0">' for vertical

INPUTS:
$post Corcel\Model\Post | BethelChika\Laradmin\WP\Models\Post 
$summary int {0,1} Prints summary of post when ==1/true
$class string The css class 
--}}

    <article class="blog-post {{$class??''}}" title="{{$post->title}}">


                <div class="blog-post-inner">
                    <div class="img-box blog-post-inner-item">
                        <a href="{{route('post',$post->post_name)}}" ><img src="{{$post->image?$post->getFeaturedThumb():'https://via.placeholder.com/728x400.png?text='.urlencode('Read More')}}" alt="{{$post->title}}"></a>
                    </div>
                    <div class="caption blog-post-inner-item">
                        
                        <h4 class="title"><a href="{{route('post',$post->post_name)}}" >
                                @if(strlen($post->title)>50)
                                    {{substr($post->title,0,47)}}...
                                @else
                                    {{$post->title}}
                                @endif
                            </a>
                        </h4>
                        <div class="date">
                            <small>
                                <i class="fas fa-clock"></i> {{$post->created_at->diffForHumans()}}
                            </small>
                        </div>
                        @if(isset($summary) and $summary)
                            <p class="summary">
                                @if(strlen($post->post_excerpt)>80)
                                    {{substr($post->post_excerpt,0,77)}}...
                                @else
                                    {{$post->post_excerpt}}
                                @endif

                            </p>
                        
                        @endif
                        
                    </div>
                </div>
        
        
    </article>
