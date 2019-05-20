{{-- Display an Autoform for viewing
    INPUT
    $form Autoform The Autoform object.
    $pageTitle string The page title.

--}}
@extends('laradmin::user.layouts.app')

@section('content')


<section role="main" class="section section-subtle section-full-page section-diffuse section-light-bg">
    <div class="container-fluid">
        <div class="form-page autoform index">
            <div class="header ">
                <h1 class="heading-2 page-title">{{ucfirst($form->getPack())}}</h1>
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
                    
                    <h3 class="title heading-3">{{$pageTitle}}</h3> 
                </div>
            </div>
            
            
            @include ('laradmin::inc.msg_board')

            
                    
            @if($form->indexDescription)<p class=" fainted-08"><small>{{$form->indexDescription}}</small></p>@endif
            @includeIf($form->getIndexTop())   
            

            @foreach($form->getGroupedFields() as $group_name=> $fields)
            
                
                <div class="group">
                    @if( $form->getGroup($group_name) and $form->getGroup($group_name)->label){{--Calling getGroup(...) a few times b/c of trying to avoid using @php to keep most things Blade--}}
                        <h3  class="heading-6  group-label">{{$form->getGroup($group_name)->label}}</h3>
                        {{-- @if($form->getGroup($group_name)->editDescription) --}}
                            <span class="description">{{$form->getGroup($group_name)->indexDescription}}</span>
                        {{-- @endif --}}
                    
                    @else 
                        {{-- @if(!$loop->first)<hr class=" list-separator">@endif  --}}
                    @endif  
                    
                    <div class="row row-c no-elevation">
                        <div class="col-md-12">
                            @include('laradmin::form.index_fields',[$fields])
                            

                        {{-- @foreach($fields as $field)
                                <div class="row">
                                        <div class="col-xs-6 col-md-3 "><span class="">{{$field->label}} @if($field->unit) <em>{{$field->unit}}</em> @endif </span></div>
                                        <div class="col-xs-6 col-md-9 ">
                                            {{$field->value}} <a class="fainted-04" href="{{$form->getEditLink()}}" title="Edit profile"> <i class="fas fa-pen"></i></a>     
                                        </div>
                                </div> 
                            @endforeach --}}

                        </div>
                    </div>

                </div>
                
            @endforeach

            <div class="text-right padding-top-x2"><a class="btn btn-primary btn-xs" href="{{$form->getEditLink()}}" title="Edit profile"> <i class="fas fa-user-edit"></i> {{__('Edit')}}</a></div>
        
            @if($form->indexBottomMessage)<p class=" fainted-08"><small>{{$form->indexBottomMessage}}</small></p>@endif
            @includeIf($form->getEditBottom())
            
        </div>
        

    </div>
</section>
@endsection
