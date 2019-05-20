
@extends('laradmin::user.layouts.app')

@section('content')

<section role="main" class="section section-first section-info section-full-page">
    <div class="container">
        <div class="row">
            
            <div class="col-md-1 padding-top-x10">
                <a class="heading-1 text-white" href="{{route('user-security')}}" title="Back to security">
                        <span class="iconify" data-icon="entypo-chevron-thin-left" data-inline="false"></span>
                    <noscript><i class="fas fa-chevron-left"></i></noscript>
                </a>
            </div>
            
            <div class="col-md-8">
                
                <h1 class="heading-1 content-title">{{$pageTitle??'Edit profile'}}
                    
                </h1>
                <p class=" fainted-08"><small>Note that for security reasons, your authentication for this page expires fast. So please make your edit as quick as you can!</small></p>
            </div>
        </div>
        <div class="row row-c">

            <div class="col-md-9 ">

                @include ('laradmin::inc.msg_board')
                @include('laradmin::inc.email_confirmation_prompt')


                <form class="form-horizontal" role="form" method="POST" action="{{route('user-edit-password')}}">
                    {{ method_field('PUT') }}
                    {{ csrf_field() }}

                    
                    

                    
                    
                    {{--@component('laradmin::blade_components.input_password',['name'=>'password','label'=>'Confirm current password','required'=>'required'])
                    
                    @endcomponent 
                    --}}
                    @component('laradmin::blade_components.input_password',['name'=>'new_password','label'=>'New password','required'=>'required'])
                    @endcomponent 

                    @component('laradmin::blade_components.input_password',['name'=>'new_password_confirmation','label'=>'New password confirmation','required'=>'required'])
                    @endcomponent 
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            
                            <a class="btn btn-subtle" href="{{route('user-security')}}">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary ">
                                Update
                            </button>
                        </div>
                    </div>
            
                </form>

                
                
            </div>
            <div class="col-md-3 ">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <span class="heading-6 panel-title" >Password recommendations</span>
                    </div>
                    <div class="panel-body">
                        <ul>
                            <li><small> Password should be 6 characters or more</small></li>
                            <li><small> Use numbers, alpha-numeric and special characters</small></li>
                            <li><small>Try to make it as random as possible.</small></li>
                        </ul>
                    </div>
                </div>
                
            </div>
            
        
        </div>
        <div class="padding-top-x6">
            <h6>Note on passwords</h6>
            <ul class="fainted-08">
                <li>Make sure your password is safe</li>
                <li>Do npt reuse password used on other website and packages</li>
                <li>Change your password regularly <br>...</li>
            </ul>
        </div>
    </div>
</section>

@endsection


