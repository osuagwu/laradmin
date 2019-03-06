@extends('laradmin::cp.layouts.app')
@section('page-top')
<ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('cp')}}">Control panel</a></li>
                <li class="breadcrumb-item active">User group maps</li>
</ol>
<h1 class="page-title">Users group maps &#8213; {{$user->name}}</h1>
@endsection
@section('content')

            
            
                
            <form class="form-vertical" role="form" method="POST" action="{{route('cp-user-group-map-updates',$user->id)}}" onsubmit="var ug_ids=[];jQuery('#user_groups_mapped option').each(function(){ug_ids.push($(this).val());}); jQuery('#member_of').val((ug_ids.join()));">
                {{ method_field('PUT') }}
                {{ csrf_field() }}
                <input type="hidden" name="member_of" id="member_of" value="" />
                <div class="form-group ">
                    <div class="row">
                        <div class="col-md-5 {{$errors->has('member_of')? 'has-error':''}}">
                            <h4>Member of </h4>
                            <select class="form-control"  id="user_groups_mapped" multiple="multiple">  
                            @foreach($user_groups_mapped as $ugm)
                            <option value="{{$ugm->id}}">{{$ugm->name}} &#8213; {{$ugm->description}}</option>
                            @endforeach
                            </select>
                            @if ($errors->has('member_of')) <p class="help-block">{{ $errors->first('member_of') }}</p> @endif
                        </div>
                    

                        <div class="col-md-2 text-center">
                            <br>
                            <button type="button"  class="btn btn-primary" onclick="jQuery('#user_groups_mapped option:selected').remove().appendTo('#user_groups_unmapped')">
                                <span class="glyphicon glyphicon-forward"></span>
                            </button>
                            <br>
                            <br>
                            <button type="button" class="btn btn-primary" onclick="jQuery('#user_groups_unmapped option:selected').remove().appendTo('#user_groups_mapped')">
                                <span class="glyphicon glyphicon-backward"></span>
                            </button>
                        </div>
                    
                        <div class="col-md-5 ">
                            <h4>Not member of</h4>         

                            <select class="form-control"  id="user_groups_unmapped" multiple="multiple">                       
                            @foreach($user_groups_unmapped as $ugu)
                                <option value="{{$ugu->id}}">{{$ugu->name}} &#8213; {{$ugu->description}}</option>
                            
                            
                            @endforeach 
                            </select>
                        </div>
                    </div>
                </div>
                
                
                <div class="form-group">
                    <div class="col-md-12 text-center">
                        
                        <a class="btn btn-warning" href="{{route('cp-user',$user->id)}}">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Update
                        </button>
                    </div>
                </div>
            </form>
            

@endsection
