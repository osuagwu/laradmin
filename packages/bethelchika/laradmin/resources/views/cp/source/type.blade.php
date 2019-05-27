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
    <a class="btn btn-primary btn-sm" href="{{route('cp-source-create')}}">Link a source</a>
</div>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                           
                            <th> Name
                            
                            </th>
                            <th>Type</th>
                            <th>Description</th>
                            <th></th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sources as $source)
                        <tr>
                            <td><a href="{{route('cp-source-show',[$source->id])}}"> {{$source->name}} <span class="glyphicon glyphicon-eye-open"></span></a></td>
                            <td>{{$source->type}}</td>
                            <td>{{$source->description}}</td>
                            <td>
                                <a href="{{route('cp-source-edit',$source->id)}}"><i class="fas fa-edit"></i></a>
                                
                                @component('laradmin::blade_components.table_row_delete',['formAction'=>route('cp-source-edit',$source->id)])
                                @endcomponent
                            </td>
                        </tr>
                        
                        
                        @endforeach
                    </tbody>
                </table>
                @unless(count($sources))
                    <p class="alert alert-warning">The are no entries</p>
                @endunless
            </div>
            {{$sources->links()}}

@endsection
