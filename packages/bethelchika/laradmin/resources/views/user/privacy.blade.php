@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.content_manager.stacks')
@section('content')
<section role="banner" class="section section-primary  section-title @include($laradmin->theme->defaultFrom().'wp.inc.section_gradient',['page'=>$page,'scheme'=>'primary','brand2'=>'success','direction'=>'right','fainted'=>70]) ">
    <div class="container">
        <h1 class=" page-title heading-1 ">{{$page->title??__('Privacy policy')}} 
            @if(isset($page))<br><small class="text-white">Last Updated: <time datetime="{{$page->updated_at}}">{{$page->updated_at->setTimeZone(session('timezone'))->format('l jS \\of F Y h:i:s A')}}</time></small> @endif
        </h1>
       
    </div>

</section>
<section role="main" class="section section-default  section-title padding-top-x10"  >
        <div class="section-overlay">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 ">

                        @if(!$page and !$privacy_content)
                            @include('laradmin::user.partials.privacy')
                        @else
                            @if($privacy_content)
                                {!!$privacy_content!!}
                            @else
                                {!!$page->contentFiltered!!}
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>



@endsection
