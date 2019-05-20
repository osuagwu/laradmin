@extends('laradmin::cp.layouts.app')
@section('page-top')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('cp')}}">Control panel</a></li>
    <li class="breadcrumb-item active">Sources</li>
</ol>
<h1 class='page-title'>{{$pageTitle??' '}}</h1>
@endsection

@section('content')
    <ul class="list-group">
        @foreach ($sources as $source)
            <li class="list-group-item"><a href="{{route('cp-source-type',[$source])}}">{{$source}} </a></li>
        @endforeach
    </ul>
  
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                           
                            <th> Source name
                            
                            </th>
                            <th>Source type</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tables as $table)
                        <tr>
                            <td><a href="{{route('cp-source-show-table',$table->name)}}"><span class="glyphicon glyphicon-th"></span> {{$table->label}} <span class="glyphicon glyphicon-eye-open"></span></a></td>
                            <td>Table</td>
                        </tr>
                        
                        @endforeach
                    </tbody>
                </table>
            </div>
            

@endsection
