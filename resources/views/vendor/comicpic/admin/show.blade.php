<div>
    <nav>
        <ul class="nav nav-tabs">
            <li ><a href="{{route('comicpic.admin')}}">Data view</a></li>
            <li ><a href="{{route('comicpic.admin-edit-settings')}}">Change settings</a></li>
            
        <ul>
    <nav>
</div>             
<br>
<br>

<form  method="POST" action="{{route('comicpic.admin-delete',$comicpic->id)}}">
    {{method_field('DELETE')}}
    {{csrf_field()}}
    <button class="btn btn-danger" title="Delete" ><i class="fa fa-times"></i> Delete</button>
</form>

<h3>{{$comicpic->title}} </h3>
<div class="description">{{$comicpic->description}}</div>  
<div>
    <img class="" style="width:100%;height:auto;" src="{{Storage::disk('public')->url($comicpic->medias[0]->getFullName())}}" alt="{{$comicpic->title}}" />   
</div>
  