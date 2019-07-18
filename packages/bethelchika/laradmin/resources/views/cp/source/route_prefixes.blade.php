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
                           
                           <th> Prefix</th>
                            
                            
                            
                            
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prefixes as $prefix)
                        <tr>
                            
                            <td>
                                <a href="{{route('cp-source-show-route_prefix',['name'=>$prefix])}}"> 
                                    {{$prefix}}
                                    @if($laradmin->permission->hasEntry('route_prefix',$prefix))
                                        <span class="label label-warning" title="Has permission">
                                            <i class="fas fa-lock" > </i> 
                                        </span>
                                    @endif 
                                    <span class="glyphicon glyphicon-eye-open"></span>
                                </a>
                            </td>
                            
                        </tr>
                        
                        @endforeach
                    </tbody>
                </table>
            </div>
            

@endsection
