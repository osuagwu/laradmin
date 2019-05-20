@extends('laradmin::cp.layouts.app')

@section('page-top')

            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('cp')}}">Control panel</a></li>
                <li class="breadcrumb-item"><a href="{{route('cp-users')}}">Users</a></li>
                <li class="breadcrumb-item active">{{$user->name}}</li>
            </ol>

            <h1 class="page-title">Profile  &#8213; {{$user->name}}</h1>
@endsection
@section('content')
            

       
            
            <div class="row">
                <div class="col-md-3">
                <div class="text-center"  >  
                    <button  class="btn btn-circle btn-success btn-lg"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> </button>
                </div>
                @if($user->status==-1)
                    <br />
                    <div class="alert alert-warning">
                        <span class="glyphicon glyphicon-remove text-danger" ></span>
                        <span class="text-danger" >Unconfirmed email</span>
                        
                        @if($user->is_active)
                            <br />
                            <span  ><span class="glyphicon glyphicon-ok " ></span> Active account</a>
                        @else 
                            <br />  
                            <span ><span class="glyphicon glyphicon-remove" ></span> Disabled account</a>
                        @endif
                        <br />
                        <div class="btn-group btn-group-vertical" role="group" aria-label="User status and active state controls">
                            <a href="{{route('cp-send-email-confirmation',$user->id)}}" class="btn btn-primary"> <span class="glyphicon glyphicon-envelope" > </span> Send confirm link</a>
                             <a href="{{route('cp-user-message-create')}}?user={{$user->id}}" class="btn btn-primary"><span class="glyphicon glyphicon-envelope" > </span> Send message</a>
                            <a href="{{route('cp-email-confirmation',$user->id)}}" class="btn btn-primary"><span class="glyphicon glyphicon-ok" > </span> Accept email</a>
                            @if($user->is_active)
                                <a href="{{route('cp-user-disable',$user->id)}}" class="btn btn-primary"><span class="glyphicon glyphicon-remove text-danger" ></span> Disable account</a>
                            @else
                                <a href="{{route('cp-user-enable',$user->id)}}" class="btn btn-primary"><span class="glyphicon glyphicon-ok text-info" ></span> Enable account</a>
                            @endif
                        </div>
                    </div>
                @else
                    
                    <br />
                    <div class="alert alert-info">
                        <div class="label label-info">
                            <span class="glyphicon glyphicon-ok" ></span>
                            <span> Confirmed </span>
                        </div>
                        @if($user->is_active)
                            <br />
                            <span class="text-info"><span class="glyphicon glyphicon-ok" > </span> Active account</a>
                        @else  
                            <br /> 
                            <span class="text-danger"><span class="glyphicon glyphicon-remove " > </span> Disabled account</a>
                        @endif
                        
                        <br />
                        <div class="btn-group btn-group-vertical" role="group" aria-label="User active state controls">
                            <a href="{{route('cp-user-message-create')}}?user={{$user->id}}" class="btn btn-primary"><span class="glyphicon glyphicon-envelope" > </span> Send message</a>
                            @if($user->is_active)
                                <a href="{{route('cp-user-disable',$user->id)}}" class="btn btn-primary"><span class="glyphicon glyphicon-remove text-danger" ></span> Disable account</a>
                            @else
                                <a href="{{route('cp-user-enable',$user->id)}}" class="btn btn-primary"><span class="glyphicon glyphicon-ok text-info" ></span> Enable account</a>
                            @endif
                        </div>
                    </div>
                    
                @endif            
                                       
                    
                </div>
                <div class="col-md-9">
                    <h4>Personal</h4> <hr class="hr">
                    <div class="row">
                        <div class="col-xs-6 col-md-3 text-right"><strong>Screen name</strong></div>
                        <div class="col-xs-6 col-md-9 ">{{$user->name}}</div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6 col-md-3 text-right"><strong>First names</strong></div>
                        <div class="col-xs-6 col-md-9 ">{{$user->first_names}}</div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6 col-md-3 text-right"><strong>Last name</strong></div>
                        <div class="col-xs-6 col-md-9 ">{{$user->last_name}}</div>
                    </div>

                    <div class="row">
                        <div class="col-xs-6 col-md-3 text-right"><strong>Year of birth</strong></div>
                        <div class="col-xs-6 col-md-9 ">{{$user->year_of_birth}}</div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6 col-md-3 text-right"><strong>Gender</strong></div>
                        <div class="col-xs-6 col-md-9 ">{{ucfirst($user->gender)}}</div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6 col-md-3 text-right"><strong>Faith</strong></div>
                        <div class="col-xs-6 col-md-9 ">{{ucfirst($user->faith)}}</div>
                    </div>
                
                    <p class="text-right"><a href="{{route('cp-user-edit',$user->id)}}">Edit</a></p>
                    
                    <h4>Contact details</h4><hr class="hr">
                    <div class="row">
                        <div class="col-xs-6 col-md-3 text-right"><strong>E-mail</strong></div>
                        <div class="col-xs-6 col-md-9 ">{{$user->email}}</div>
                    </div>
                    

                    <h4>Security</h4><hr class="hr">
                    <div class="row">
                        <div class="col-xs-6 col-md-3 text-right"><strong>Password</strong></div>
                        <div class="col-xs-6 col-md-9 ">*******  <p class="text-right"><a href="{{route('cp-user-edit',$user->id)}}">Reset</a></p></div>
                    </div>
                    

                    <h4>Location</h4><hr class="hr">

                    <div class="row">
                        <div class="col-xs-6 col-md-3 text-right"><strong>Country</strong></div>
                        <div class="col-xs-6 col-md-9 ">{{ __( 'laradmin::list_of_countries.'.$user->country )}}</div>
                    </div>
                    
                    <p class="text-right"><a href="{{route('cp-user-edit',$user->id)}}">Edit</a></p>

                    <h4>User group membership</h4><hr class="hr">

                        
                    @foreach($user_groups as $user_group)
                            <span class="label label-primary"><strong>{{$user_group->name}}</strong></span> &#8213; {{$user_group->description}}
                            <br>
                    @endforeach
                    
                    <p class="text-right"><a href="{{route('cp-user-group-map-edits',$user->id)}}">Edit</a></p>
                    <div class="well well-sm">
                        <a href="{{URL::previous() }}" class="btn btn-default">Back</a>
                    </div>
                </div>
        
            </div>
                  

@endsection
