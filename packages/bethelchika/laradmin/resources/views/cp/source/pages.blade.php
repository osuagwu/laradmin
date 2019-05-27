@extends('laradmin::cp.layouts.app')
@section('page-top')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('cp')}}">Control panel</a></li>
    <li class="breadcrumb-item active">Sources</li>
</ol>
<h1 class='page-title'>{{$pageTitle??' '}}</h1>
@endsection

@section('content')

    {{$pages->links()}}
  
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                           
                            <th> ID
                            
                            </th>

                            <th>Title</th>
                            
                            <th> </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pages as $page)
                        <tr>
                            <td>{{$page->ID}}</td>
                            <td ><a title="{{$page->title}}" href="{{route('cp-source-show-page',[$page->ID])}}"> {{str_limit($page->title,30)}} <span class="glyphicon glyphicon-eye-open"></span></a></td>
                            <td><a role="button" aria-label="Edit {{$page->title}}" title="Edit" href="{{$page->getEditLink()}}"><i class="fas fa-edit"></i></a></td>
                        </tr>
                        
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{$pages->links()}}

@endsection
