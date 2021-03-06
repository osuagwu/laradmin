@extends('laradmin::cp.layouts.app')
@section('page-top')
    <h1 class='page-title'>{{$pageTitle??' '}}</h1>
@endsection

@section('content')

        

        
            
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="cp-object" >
                            
                            <h3><a href="{{route('cp-users')}}" style="text-decoration:none;">
                                    <span class="	glyphicon glyphicon-user"> </span>
                                    <br>
                                    Users 
                                    <br>
                                    <span class="small">User management</span>
                                </a>
                            </h3>   
                            
                        </div>                     
                    </div>
                    <div class="col-md-4 text-center">
                        
                        <div class="cp-object" >
                        
                            <h3><a href="{{route('cp-user-groups')}}" style="text-decoration:none;">
                                    <span class="	glyphicon glyphicon-user"> </span><span class="	glyphicon glyphicon-user"> </span>
                                    <br>
                                    User groups 
                                    <br>
                                    <span class="small">Manage groups</span>
                                </a>
                            </h3>   
                        
                        </div>
                                               
                    </div>
                    
                    
                    <div class="col-md-4 text-center">
                        <div class="cp-object" >
                            
                            <h3><a href="{{route('cp-source-types')}}" style="text-decoration:none;">
                                    <i class="fas fa-hdd"> </i>
                                    <br>
                                    Sorces 
                                    <br>
                                    <span class="small">Source management</span>
                                </a>
                            </h3>   
                            
                        </div> 
                                             
                    </div>
                </div>

                <div class="row">
                    
                    <div class="col-md-4 text-center">
                         
                        <div class="cp-object" >
                        
                            <h3><a href="{{route('cp-settings')}}" style="text-decoration:none;">
                                    <span class="	glyphicon glyphicon-ok"> </span>
                                    <br>
                                    Settings 
                                    <br>
                                    <span class="small">Settings</span>
                                </a>
                            </h3>   
                        
                        </div>                   
                    </div>
                    

                    <div class="col-md-4 text-center"> 
                        
                        <div class="cp-object" >
                        
                            <h3><a href="{{route('cp-user-message-index')}}" style="text-decoration:none;">
                                    <span class="	glyphicon glyphicon-envelope"> </span>
                                    <br>
                                    c User Message
                                    <br>
                                    <span class="small">Control panel message</span>
                                </a>
                            </h3>   
                        
                        </div>
                    </div> 

                    <div class="col-md-4 text-center"> 
                        
                        <div class="cp-object" >
                        
                            <h3><a href="{{route('cp-notification-index')}}" style="text-decoration:none;">
                                    <span class="	glyphicon glyphicon-bell"> </span>
                                    <br>
                                    c Notifications
                                    <br>
                                    <span class="small">Control panel notifications</span>
                                </a>
                            </h3>   
                        
                        </div>
                    </div> 
                   
                </div>

            
               
                {{--  <div class="row">
                    <div class="col-md-4 text-center"> 

                        <div class="cp-object" >
                                
                                    <h3><a href="{{route('cp-users')}}" style="text-decoration:none;">
                                            <span class="	glyphicon glyphicon-arrow-left"> </span>
                                            <br>
                                            Reincarnate 
                                            <br>
                                            <span class="small">App:settings</span>
                                        </a>
                                    </h3>   
                                
                        </div>
                    </div>
                    <div class="col-md-4 text-center"> 
                        <div class="cp-object" >
                        
                            <h3><a href="#" style="text-decoration:none;">
                                    <span class="	glyphicon glyphicon-ok"></span>/<span class="	glyphicon glyphicon-remove"> </span>
                                    <br>
                                    True or FALSE
                                    <br>
                                    <span class="small">True/False concept?</span>
                                </a>
                            </h3>   
                        
                        </div>                    
                    </div>
                    <div class="col-md-4 text-center"> 
                        <div class="cp-object" >
                        
                            <h3><a href="#" style="text-decoration:none;">
                                    <span class="	glyphicon glyphicon-random"> </span>
                                    <br>
                                    Process
                                    <br>
                                    <span class="small">Flow diagram of stuff</span>
                                </a>
                            </h3>   
                        
                        </div>                    
                    </div>
                </div>   --}}
                <div class="row">
                    {{--  <div class="col-md-4 text-center"> 
                        
                        <div class="cp-object" >
                        
                            <h3><a href="{{route('cp-users')}}" style="text-decoration:none;">
                                    <span class="	glyphicon glyphicon-download"> </span>
                                    <br>
                                    Backup
                                    <br>
                                    <span class="small">Backup site</span>
                                </a>
                            </h3>   
                        
                        </div>                  
                    </div>  --}}


                    @if(config('laradmin.wp_enable',false))
                    <div class="col-md-4 text-center">
                        <div class="cp-object" >
                        
                            <h3><a href="{{url(config('laradmin.wp_rpath').'/wp-admin')}}/edit.php?post_type=page" style="text-decoration:none;">
                                    <span class="	glyphicon glyphicon-book"> </span>
                                    <br>
                                    Pages <small><i class="fas fa-external-link-alt"></i></small>
                                    <br>
                                    <span class="small">Manage webpages</span>
                                </a>
                            </h3>   
                        
                        </div> 
                                               
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="cp-object" >
                        
                            <h3><a href="{{url(config('laradmin.wp_rpath').'/wp-admin')}}/edit.php?post_type=post" style="text-decoration:none;">
                                    <span class="	glyphicon glyphicon-phone"> </span>
                                    <br>
                                    Posts <small><i class="fas fa-external-link-alt"></i></small> 
                                    <br>
                                    <span class="small">Manage Posts</span>
                                </a>
                            </h3>   
                        
                        </div>                        
                    </div>
                    @endif
                    

                </div>   
            


                
 
@endsection
