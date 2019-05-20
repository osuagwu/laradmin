@extends('laradmin::cp.layouts.app')

@section('page-top')

<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('cp')}}">Control panel</a></li>
    <li class="breadcrumb-item active">Users</li>
</ol>
<h1 class='page-title'>{{'Users'}}</h1>
@endsection

@section('content')

            <!-- list controls-->
            @component('laradmin::blade_components.table_nav',[
                                                            'tableName'=>'users',
                                                            'actions'=>[
                                                                'delete'=>['formAction'=>route('cp-users-delete'),'label'=>'Delete selected user'],
                                                            ],
                                                            'links'=>[
                                                                ['label'=>'Clear Search','url'=>URL::current()],
                                                                ['label'=>'New user','url'=>route('cp-user-create')],
                                                                ['label'=>'Message all users','url'=>route('user-message-create')],
                                                            ]
                                                        ])
                @endcomponent 
                           
            <!-- start listing data-->    
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>
                                @component('laradmin::blade_components.table_row_checkbox',['tableName'=>'users'])
                                @endcomponent
                            </th>
                            <th>Screen name
                                @component('laradmin::blade_components.sort_links',['orderBy'=>'name','currentOrder'=>$currentOrder])
                                @endcomponent  
                            </th>
                            <th>Firstname
                                @component('laradmin::blade_components.sort_links',['orderBy'=>'first_name','currentOrder'=>$currentOrder])
                                @endcomponent
                            </th>
                            <th>Lastname
                                @component('laradmin::blade_components.sort_links',['orderBy'=>'last_name','currentOrder'=>$currentOrder])
                                @endcomponent
                            </th>
                            <th>Email
                                @component('laradmin::blade_components.sort_links',['orderBy'=>'email','currentOrder'=>$currentOrder])
                                @endcomponent
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                            @component('laradmin::blade_components.table_row_checkbox',['tableName'=>'users','value'=>$user->id,'isHeadCheckbox'=>false])
                            @endcomponent
                            </td>
                            <td><a href="{{route('cp-user',$user->id)}}">{{$user->name}}</a></td>
                            <td>{{$user->first_names}}</td>
                            <td>{{$user->last_name}}</td>
                            <td>{{$user->email}}</td>
                            <td>
                                <a href="{{route('cp-user-edit',$user->id)}}" title="Edit" > <span class="glyphicon glyphicon-edit"></span></a>
                                @component('laradmin::blade_components.table_row_delete',['formAction'=>route('cp-user-delete',$user->id)])
                                @endcomponent
                                
                                <a title="Message user" href="{{route('cp-user-message-create')}}?user={{$user->id}}"> <span class="glyphicon glyphicon-envelope"> </span> </a>
                                
                                 <a title="View user" href="{{route('cp-user',$user->id)}}"> <span class="glyphicon glyphicon-eye-open"> </span> </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{$users->appends(request()->all())->links()}}
           

@endsection
