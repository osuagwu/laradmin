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
                           
                            <th> Table name
                            
                            </th>
                            <th>Connection</th>
                            <th>Database</th>
                            
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tables as $table)
                        <tr title="{{$table->connection_info['prefix']}}">
                            <td>
                                <a  href="{{route('cp-source-show-table',[$table->name,'connection'=>$table->connection,'prefix'=>$table->connection_info['prefix'],'database'=>$table->connection_info['database']])}}">
                                    <span class="glyphicon glyphicon-th"></span> {{$table->label}} 
                                    
                                    @if($laradmin->permission->hasEntry('route',BethelChika\Laradmin\Source::getTableSourceId($table->connection,$table->connection_info['database'],$table->connection_info['prefix'],$table->name)))
                                        <span class="label label-warning" title="Has permission">
                                            <i class="fas fa-lock" > </i> 
                                        </span>
                                    @endif 

                                    <span class="glyphicon glyphicon-eye-open"></span>
                                </a>
                            </td>
                            <td>{{$table->connection}}</td>
                            <td>{{$table->connection_info['database']}}</td>
                            
                        </tr>
                        
                        @endforeach
                    </tbody>
                </table>
            </div>
            

@endsection
