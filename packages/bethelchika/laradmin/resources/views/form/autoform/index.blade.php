@extends('laradmin::user.layouts.app')

@section('content')


<section class="section section-subtle section-full-page section-diffuse section-light-bg">
    <div class="container-fluid">
        <div class="form-page index">
            <div class="header ">
                <h1>{{ucfirst($form->getPack())}}</h1>
                <nav class="nav nav-tabs nav-flat">
                    @include('laradmin::menu',['tag'=>$form->manager()->autoformPackToMenu($form->getPack())])
                </nav>
                <div class="formbar">
                    <div class="nav-back-to">
                        <a href="{{$form->getNavCloseLink()}}">
                            <span class="iconify " data-icon="entypo-chevron-thin-left" data-inline="false"></span>
                            <noscript><i class="fas fa-chevron-left"></i></noscript>
                        </a>
                    </div>
                    <div class="nav-close">
                        <a href="{{$form->getNavCloseLink()}}">
                            <span class="iconify" data-icon="zmdi:close" data-inline="false"></span>
                            <noscript><i class="fas fa-times"></i></noscript>
                        </a>
                    </div>
                    
                    <h1 class="heading-2 page-title">{{$pageTitle}}</h1> 
                </div>
            </div>
            
            
            @include ('laradmin::inc.msg_board')

            
                    
            @if($form->indexDescription)<p class=" fainted-08"><small>{{$form->indexDescription}}</small></p>@endif
            @includeIf($form->getIndexTop())   
            

            @foreach($form->getGroupedFields() as $group_name=> $fields)
            
                @if(str_is($group_name,'__group__'))
                    @continue
                @endif
                <div class="group">
                    @if(!str_is($group_name,'__') and $form->getGroup($group_name))
                        <h3 id="PD-personal-information" class="heading-6 heading-slashes">{{$form->getGroup($group_name)->label??ucfirst($group_name)}}</h3>
                        {{-- @if($form->getGroup($group_name)->editDescription) --}}
                            <span class="description">{{$form->getGroup($group_name)->indexDescription}}</span>
                        {{-- @endif --}}
                        
                    @endif
                    <div class="row row-c no-elevation">
                        <div class="col-md-12">
                            
                            

                            @foreach($fields as $field)
                                <div class="row">
                                        <div class="col-xs-6 col-md-3 "><span class="">{{$field->label}} @if($field->unit) <em>{{$field->unit}}</em> @endif </span></div>
                                        <div class="col-xs-6 col-md-9 ">
                                            {{$field->value}} <a class="fainted-04" href="{{$form->getEditLink()}}" title="Edit profile"> <i class="fas fa-pen"></i></a>     
                                        </div>
                                </div>
                            @endforeach

                        </div>
                    </div>

                </div>
                
            @endforeach

            <div class="text-right padding-top-x2"><a class="btn btn-primary btn-xs" href="{{$form->getEditLink()}}" title="Edit profile"> <i class="fas fa-user-edit"></i> {{__('Edit profile')}}</a></div>
        
            @if($form->indexBottomMessage)<p class=" fainted-08"><small>{{$form->indexBottomMessage}}</small></p>@endif
            @includeIf($form->getEditBottom())
        </div>
        

    </div>
</section>
@endsection
