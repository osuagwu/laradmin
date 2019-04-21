
@extends('laradmin::user.layouts.app')

@section('content')
<section class="section section-full-page section-subtle section-diffuse section-light-bg">
    <div class="container-fluid">
        <div class="form-page autoform edit">
            <div class="header ">
                <div class="formbar">
                    <div class="nav-back-to ">
                        <a class=" " href="{{$form->getLink()}}" title="Back">
                            <span class="iconify " data-icon="entypo-chevron-thin-left" data-inline="false"></span>
                            <noscript><i class="fas fa-chevron-left"></i></noscript>
                        </a>
                    </div>
                    <div class="nav-close ">
                        <a  class="" href="{{$form->getNavCloseLink()}}">
                            <span class="iconify" data-icon="zmdi:close" data-inline="false"></span>
                            <noscript><i class="fas fa-times"></i></noscript>
                        </a>
                    </div>
                    
                    <h1 class=" title heading-2 page-title">{{$pageTitle}}</h1> 
                </div>
            </div>
            <!-------------end header------------------>
        
            
                
            
            @if($form->editDescription)<p class=" fainted-08"><small>{{$form->editDescription}}</small></p>@endif
            @includeIf($form->getEditTop())
            @include ('laradmin::inc.msg_board')
            @include('laradmin::inc.email_confirmation_prompt')


            <form class="form-horizontal" role="form" method="@if(str_is(strtolower($form->method),'get')){{'GET'}}@else{{'POST'}}@endif" action="{{$form->getEditLink()}}"
                @if($form->hasImageField($form->getFields())) enctype="multipart/form-data" @endif
            >
                @if(!in_array(strtolower($form->method),['get','post']))
                    {{ method_field($form->method) }}
                @endif
                {{ csrf_field() }}

                @foreach($form->getGroupedFields() as $group_name=> $fields)
                    
                    <div class="group">
                        @if($form->getGroup($group_name) and $form->getGroup($group_name)->label)
                            <h6 class="label label-warning ">{{$form->getGroup($group_name)->label}}</h6>
                            {{-- @if($form->getGroup($group_name)->editDescription) --}}
                                <span class="description">{{$form->getGroup($group_name)->editDescription}}</span>
                            {{-- @endif --}}
                            
                            <hr class=" list-separator">
                        @else 
                            @if(!$loop->first)<hr class=" list-separator">@endif 
                        @endif

                        @component('laradmin::form.edit_fields',['fields'=>$fields])
                        @endcomponent
                    </div>
                @endforeach
                
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        
                        <a class="btn btn-subtle" href="{{$form->getLink()}}">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Update
                        </button>
                    </div>
                </div>
                @if($form->editBottomMessage)<p class=" fainted-08"><small>{{$form->editBottomMessage}}</small></p>@endif
                @includeIf($form->getEditBottom())
            </form>
                    
           
              
        </div>
    </div>
</section>
@endsection


