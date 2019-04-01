{{----}}
@include('comicpic::scripts')
<section class="section section-primary section-offset-mainbar-sides section-offeset-mainbar-top  section-title section-diffuse section-light-bg ">
    <div class=" content-padding-left content-padding-right">
        <ol class="breadcrumb bg-transparent">
            <li class="breadcrumb-item active">Comicpic</li>
        </ol>
        <h1 class="heading-3 content-title   ">Welcome to ComicPic </h1>
        <nav>
            <ul class="nav nav-tabs nav-flat">
                @include('laradmin::menu',['tag'=>'primary.comicpic'])
            </ul>
        </nav>
    </div>
    
</section> 
<section class="section section-default   section-extra-padding-bottom ">     
    
        
    <div class="text-right padding-top-x2">
        <p>
            <a href="{{route('comicpic.me')}}" class="btn btn-info btn-xs">My Comicpic</a>
            <a href="{{route('comicpic.create')}}" class="btn btn-primary btn-xs">Upload</a>
            <br /><br />
        </p>
    </div>
    
    @include ('laradmin::inc.msg_board')
    <p>Please use the button below to open settings</p>
    <a class="btn btn-warning btn-lg" href="{{$laradmin->formManager->autoformLink('comicpic','user_settings')}}">Open settings</a>
    

</section>


