@extends('laradmin::cp.layouts.app')
@section('page-top')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('cp')}}">Control panel</a></li>
    <li class="breadcrumb-item "><a href="{{route('cp-source-types')}}">Sources</a></li>
    <li class="breadcrumb-item active"><span class="glyphicon glyphicon-th"></span> Table</li>
</ol>
<h1 class='page-title'>{{$pageTitle??' '}}</h1>
@endsection

@section('content')

            
            <p > <strong>Source id:</strong> {{$source_type}}</p>
            <h2>Records</h2>         
            <p > Total number of records:<strong> {{$total_rows}} </strong></p> 
            @if($total_rows)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                           
                            <th> Record events
                            
                            </th>
                            <th> Record ID
                            
                            </th>
                            <th>Date</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th> Latest record
                    
                            </th>
                            <td>@if(property_exists($latest_insert,'id')){{$latest_insert->id}}@endif</td>
                            <td>{{$latest_insert->created_at}}</td>
                        </tr>
                        <tr>
                            <th> Oldest record
                                    
                            </th>
                            <td>@if(property_exists($oldest_insert,'id')){{$oldest_insert->id}}@endif</td>
                            <td>{{$latest_insert->created_at}}</td>
                        </tr>
                        <tr>
                            <th> Latest updated record
                    
                            </th>
                            <td>@if(property_exists($latest_update,'id')){{$latest_update->id}}@endif</td>
                            <td>{{$latest_update->created_at}}</td>
                        </tr>
                        <tr>
                            <th> Oldest updated record
                    
                            </th>
                            <td>@if(property_exists($oldest_update,'id')){{$oldest_update->id}}@endif</td>
                            <td>{{$oldest_update->created_at}}</td>
                        </tr>
                    </tbody>
                </table>
                
            </div>
            @endif
            
            @include('laradmin::permission.partials.ui',['source_type'=>$source_type,'source_id'=>$source_id])
            <p ><a class="btn btn-default" href="{{URL::previous()}}">Back</a></p>

           


  
@endsection
