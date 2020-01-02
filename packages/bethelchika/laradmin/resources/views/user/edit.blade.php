
@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.content_manager.stacks')
@section('content')
<section role="main" class="section section-subtle section-full-page  section-light-bg section-diffuse section-diffuse-no-shadow section-last">
    <div class="container">
        
        <div class="row">
            {{-- <div class="col-md-2 padding-top-x10">
                <a class="heading-1 text-white" href="{{route('user-profile')}}" title="Back to settings">
                    <span class="iconify " data-icon="entypo-chevron-thin-left" data-inline="false"></span>
                    <noscript><i class="fas fa-chevron-left"></i></noscript>
                </a>
            </div> --}}
            <div class="col-md-8 col-md-offset-2   ">
                    <h1 class="heading-3 content-title text-center">{{$pageTitle??'Edit profile'}}</h1>
                    
                @include ('laradmin::inc.msg_board')
                @include('laradmin::inc.email_confirmation_prompt')

                @include('laradmin::form.edit_form',['form'=>$form])

                {{-- <form class="form-horizontal" role="form" method="POST" action="{{route('user-edit',[$form->getPack(),$form->getTag()])}}">
                    {{ method_field('PUT') }}
                    {{ csrf_field() }}

                    
                    

                    @component('laradmin::components.input_text',['name'=>'name','value'=>$user->name,'label'=>'Name (Screen name)','required'=>'required'])
                    @endcomponent 

                    @component('laradmin::components.input_text',['name'=>'first_names','value'=>$user->first_names,'label'=>'First names'])
                    @endcomponent 

                    @component('laradmin::components.input_text',['name'=>'last_name','value'=>$user->last_name,'label'=>'Last name'])
                    @endcomponent

                    @component('laradmin::components.input_text',['name'=>'year_of_birth','value'=>$user->year_of_birth,'required'=>'required','label'=>'Year of birth'])
                    @endcomponent

                    @component('laradmin::components.input_select',['name'=>'gender','value'=>$user->gender,'options'=>['female'=>'Female','male'=>'Male'],])
                    @endcomponent

                    @component('laradmin::components.input_select',['name'=>'country','value'=>$user->country,'options'=>$countries,])
                    @endcomponent

                    @component('laradmin::components.input_select',['name'=>'faith','value'=>$user->faith,'options'=>$faiths,])
                    @endcomponent
                    
                    @component('laradmin::form.fields',['fields'=>$form->getFields()])
                        
                    @endcomponent
                   
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
                
             --}}
             <p class=" fainted-05 padding-top-x5"><small>Note that for security reasons, your authentication for this page expires fairly fast. So please make your edit as quick as you can!</small></p>
            </div>
        </div>
    </div>
</section>
@endsection


