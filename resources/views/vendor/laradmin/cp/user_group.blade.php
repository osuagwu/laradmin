@extends('laradmin::cp.layouts.app')
@section('page-top')
<ol class="breadcrumb">
                <li class="breadcrumb-item" ><a href="{{route('cp')}}">Control panel</a></li>
                <li class="breadcrumb-item"><a href="{{route('cp-user-groups')}}">User groups</a></li>
                <li class="breadcrum-item active">{{$userGroup->name}}</li>
</ol>
<h1 class="page-title">{{$userGroup->name}}</h1>
@endsection
@section('content')

            
            
               
                <p><a href="{{route('cp-user-group-edit',$userGroup->id)}}">Edit</a></p>
                <p>{{$userGroup->description}}</p>

                <div class="well well-sm">
                    <a href="{{URL::previous() }}" class="btn btn-default">Back</a>
                </div>
            
            

           



@endsection
