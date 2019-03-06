
@extends('laradmin::user.layouts.app')

@section('content')
<section class="section section-first section-full-page section-info section-last">
    <div class="container">
        
        <div class="row">
            <div class="col-md-2 padding-top-x10">
                <a class="heading-1 text-white" href="{{route('user-profile')}}" title="Back to settings">
                    <span class="iconify " data-icon="entypo-chevron-thin-left" data-inline="false"></span>
                    <noscript><i class="fas fa-chevron-left"></i></noscript>
                </a>
            </div>
            <div class="col-md-8   ">
                    <h1 class="heading-1 content-title">{{$pageTitle??'Edit profile'}}</h1>
                    <p class=" fainted-08"><small>Note that for security reasons, your authentication for this page expires fairly fast. So please make your edit as quick as you can!</small></p>
                @include ('laradmin::inc.msg_board')
                @include('laradmin::inc.email_confirmation_prompt')


                <form class="form-horizontal" role="form" method="POST" action="{{route('user-update')}}">
                    {{ method_field('PUT') }}
                    {{ csrf_field() }}

                    
                    

                    @component('laradmin::blade_components.input_text',['name'=>'name','value'=>$user->name,'label'=>'Name (Screen name)','required'=>'required'])
                    @endcomponent 

                    @component('laradmin::blade_components.input_text',['name'=>'first_names','value'=>$user->first_names,'label'=>'First names'])
                    @endcomponent 

                    @component('laradmin::blade_components.input_text',['name'=>'last_name','value'=>$user->last_name,'label'=>'Last name'])
                    @endcomponent

                    @component('laradmin::blade_components.input_text',['name'=>'year_of_birth','value'=>$user->year_of_birth,'required'=>'required','label'=>'Year of birth'])
                    @endcomponent

                    @component('laradmin::blade_components.input_select',['name'=>'gender','value'=>$user->gender,'options'=>['female'=>'Female','male'=>'Male'],])
                    @endcomponent

                    @component('laradmin::blade_components.input_select',['name'=>'country','value'=>$user->country,'options'=>$countries,])
                    @endcomponent

                    @component('laradmin::blade_components.input_select',['name'=>'faith','value'=>$user->faith,'options'=>$faiths,])
                    @endcomponent
                    
                    {{--
                    <hr class="hr">
                    
                    @component('laradmin::blade_components.input_password',['name'=>'password','label'=>'Confirm current password'])
                    
                    @endcomponent 
                    
                    @component('laradmin::blade_components.input_password',['name'=>'new_password','label'=>'New password'])
                    @endcomponent 

                    @component('laradmin::blade_components.input_password',['name'=>'new_password_confirmation','label'=>'New password confirmation'])
                    @endcomponent 
                    --}}
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            
                            <a class="btn btn-subtle" href="{{route('user-profile')}}">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Update
                            </button>
                        </div>
                    </div>
            
                </form>
                
            
            </div>
        </div>
    </div>
</section>
@endsection


