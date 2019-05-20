@extends('laradmin::cp.layouts.app')
@section('page-top')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('cp')}}">Control panel</a></li>
    <li class="breadcrumb-item active">Sources</li>
</ol>
<h1 class='page-title'>{{$pageTitle??' '}}</h1>
@endsection

@section('content')

  
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                           <th>Methods</th>
                           <th> Prefix</th>
                            <th> Uri</th>
                            <th> Action</th>
                            
                            
                            
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($routes as $route)
                        <tr>
                            <td>{{implode('|',$route->methods())}}</td>
                            <td>{{$route->getPrefix()}}</td>
                            <td title="{{$route->uri()}}"><a href="{{route('cp-source-show-route',['name'=>$route->getName(),'methods'=>implode('|',$route->methods()),'prefix'=>$route->getPrefix(),'uri'=>$route->uri(),'action'=>$route->getActionName()])}}"><span class="glyphicon glyphicon-th"></span> {{str_limit($route->uri(),35)}} <span class="glyphicon glyphicon-eye-open"></span></a></td>
                            <td title="{{$route->getActionName()}}">{{str_limit($route->getActionName(),20)}}</td>
                        </tr>
                        
                        @endforeach
                    </tbody>
                </table>
            </div>
            

@endsection
