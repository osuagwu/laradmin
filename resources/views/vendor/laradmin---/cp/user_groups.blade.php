@extends('laradmin::cp.layouts.app')
@section('page-top')
<ol class="breadcrumb">
                <li class="breadcrumb-item" ><a href="{{route('cp')}}">Control panel</a></li>
                <li class="breadcrumb-item active">User groups</li>
</ol>
<h1 class="page-title">User groups</h1>
@endsection
@section('content')

            
           
                
                <!-- list controls-->
                @component('laradmin::blade_components.table_nav',[
                                                            'tableName'=>'user_groups',
                                                            'actions'=>[
                                                                'delete'=>['formAction'=>route('cp-user-groups-delete'),'label'=>'Delete selected user groups'],
                                                            ],
                                                            'links'=>[
                                                                ['label'=>'Clear Search','url'=>URL::current()],
                                                                ['label'=>'New user group','url'=>route('cp-user-group-create')],
                                                            ]
                                                        ])
                @endcomponent 
                           
                <!-- start listing data-->
                <div class="table-responsive">
                    <table class="table table-striped table-hover"  >
                        <thead>
                            <tr>
                                
                                <th>
                                    
                                    @component('laradmin::blade_components.table_row_checkbox',['tableName'=>'user_groups'])
                                    @endcomponent 
                                </th>
                                <th>ID 
                                    @component('laradmin::blade_components.sort_links',['orderBy'=>'id','currentOrder'=>$currentOrder])
                                    @endcomponent                                    
                                </th>
                                <th>Name
                                    @component('laradmin::blade_components.sort_links',['orderBy'=>'name','currentOrder'=>$currentOrder])
                                    @endcomponent                                    
                                </th>
                                <th>Description</th>                                
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($userGroups as $userGroup)
                            <tr>
                                <td>
                                    @component('laradmin::blade_components.table_row_checkbox',['tableName'=>'user_groups','value'=>$userGroup->id,'isHeadCheckbox'=>false])
                                    @endcomponent 
                                    
                                </td>
                                <td>{{$userGroup->id}}</td>
                                <td><a href="{{route('cp-user-group',$userGroup->id)}}">{{$userGroup->name}}</a></td>
                                <td>{{$userGroup->description}}</td>
                                
                                <td>
                                    <a href="{{route('cp-user-group-edit',$userGroup->id)}}" title="Edit" > <span class="glyphicon glyphicon-edit"></span></a>
                                    @component('laradmin::blade_components.table_row_delete',['formAction'=>route('cp-user-group-delete',$userGroup->id)])
                                    @endcomponent
                                </td>
                            </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                    @if(!count($userGroups))
                    <p class="alert alert-warning">No results</p>
                    @endif
                </div>
                {{$userGroups->appends(request()->all())->links()}}
            
            
           



@endsection
