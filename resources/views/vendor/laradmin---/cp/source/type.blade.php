@extends('laradmin::cp.layouts.app')
@section('page-top')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('cp')}}">Control panel</a></li>
    <li class="breadcrumb-item active">Sources</li>
</ol>
<h1 class='page-title'>{{$pageTitle??' '}}</h1>
@endsection

@section('content')
<div class="text-right">
    <a class="btn btn-primary btn-sm" href="{{route('cp-source-create')}}">Create new source</a>
</div>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                           
                            <th> Source name
                            
                            </th>
                            <th>Source type</th>
                            <th>Description</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sources as $source)
                        <tr>
                            <td><a href="{{route('cp-source-show',[$source->id])}}"><span class="glyphicon glyphicon-th"></span> {{$source->name}} <span class="glyphicon glyphicon-eye-open"></span></a></td>
                            <td>{{$source->type}}</td>
                            <td>{{$source->description}}</td>
                        </tr>
                        
                        @endforeach
                    </tbody>
                </table>
            </div>
            

@endsection
