<div>
    <nav>
        <ul class="nav nav-tabs">
            <li ><a href="{{route('comicpic.admin')}}">Data view</a></li>
            <li class="active"><a href="{{route('comicpic.admin-edit-settings')}}">Change settings</a></li>
            
        <ul>
    <nav>
</div>               
<br>
<br>

<form class="form-horizontal" role="form" method="POST" action="{{route('comicpic.admin-edit-settings')}}">
   
    {{ csrf_field() }}{{ method_field('put') }}

    @component('laradmin::blade_components.input_text',['name'=>'appname','value'=>$appname,'required'=>'required'])
    @endcomponent                    

    <div class="form-group">
        <div class="col-md-6 col-md-offset-4">
            
            <a class="btn btn-warning" href="{{route('cp-users')}}">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                Update
            </button>
        </div>
    </div>

</form>

   

       
