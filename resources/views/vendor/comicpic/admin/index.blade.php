<div>
    <nav>
        <ul class="nav nav-tabs">
            <li class="active"><a href="{{route('comicpic.admin')}}">Data view</a></li>
            <li ><a href="{{route('comicpic.admin-edit-settings')}}">Change settings</a></li>
            
        <ul>
    <nav>
</div>             
<br>
<br>




        <!-- list controls-->
        @component('laradmin::components.table_nav',[
                                                        'tableName'=>'comicpics',
                                                        'actions'=>[
                                                            'delete'=>['formAction'=>route('comicpic.admin-deletes',0),'label'=>'Delete selected item'],
                                                        ],
                                                        'links'=>[
                                                            ['label'=>'Clear Search','url'=>URL::current()],
                                                        ]
                                                    ])
            @endcomponent 
                       
        <!-- start listing data-->    
        @unless(count($comicpics))
            <p class="alert alert-warning">There is no item to display <i class="far fa-frown"></i></p>
            
        @else
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>
                            @component('laradmin::components.table_row_checkbox',['tableName'=>'comicpics'])
                            @endcomponent
                        </th>
                        <th> 
                        </th>
                        <th>
                            @component('laradmin::components.sort_links',['orderBy'=>'title','currentOrder'=>$currentOrder])
                            @endcomponent
                        </th>
                        
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($comicpics as $comicpic)
                    <tr>
                        <td>
                        @component('laradmin::components.table_row_checkbox',['tableName'=>'comicpics','value'=>$comicpic->id,'isHeadCheckbox'=>false])
                        @endcomponent
                        </td>
                        
                        <td><a href="{{route('comicpic.admin-show',$comicpic->id)}}"><img style="width:50px; height:auto;" src="{{Storage::disk('public')->url($comicpic->medias[0]->getThumbFullName())}}" alt="{{$comicpic->title}}" /> </a></td>
                        <td><a href="{{route('comicpic.admin-show',$comicpic->id)}}">{{$comicpic->title}}</a></td>
                        <td>
                            
                            @component('laradmin::components.table_row_delete',['formAction'=>route('comicpic.admin-delete',$comicpic->id)])
                            @endcomponent
                            
                            
                             <a title="View item" href="{{route('comicpic.admin-show',$comicpic->id)}}"> <span class="glyphicon glyphicon-eye-open"> </span> </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{$comicpics->appends(request()->all())->links()}}
        @endunless