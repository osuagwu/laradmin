@extends('laradmin::user.layouts.app')

@section('content')


<section class="section section-subtle" style="border-bottom:1px solid #ddd">
    @include('laradmin::user.partials.minor_nav',['scheme'=>'subtle','with_container'=>true,'with_icon'=>false,'left_menu_tag'=>'user_settings','root_tag'=>false])
</section>

<section class="section section-subtle section-full-page">
    <div class="container-fluid">
        
            <div class="sidebar-mainbar">
                {{-- sidebar control --}}
                @include('laradmin::user.partials.sidebar.init') 
                <aside class="sidebar">
                    
                    <div class="sidebar-content mCustomScrollbar" data-mcs-theme="minimal-dark">
                        {{-- sidebar content --}}
                        <div class="sidebar-close-btn" title="Close sidebar">X</div>
                        <div class="back-to-usettings"><a href="{{route('user-settings')}}" title="Back to main settings" ><i class="fas fa-chevron-circle-left"></i> Back to main settings</a></div>
                        @include('laradmin::user.partials.quick_settings')
                        
                    </div>
                </aside>
        
                    <!-- Page Content Holder -->
                <div class="mainbar">
    
                        <h1 class="heading-2 page-title">{{$pageTitle}}</h1> 
                        
                        @include ('laradmin::inc.msg_board')
                        @include('laradmin::inc.email_confirmation_prompt')

                        
                                
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
                                                        {{$field->value}} <a class="fainted-04" href="{{route('user-autoform-edit',[$form->getPack(),$form->getTag()])}}" title="Edit profile"> <i class="fas fa-pen"></i></a>     
                                                    </div>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>

                            </div>
                            
                        @endforeach

                        <div class="text-right padding-top-x2"><a class="btn btn-primary btn-xs" href="{{route('user-autoform-edit',[$form->getPack(),$form->getTag()])}}" title="Edit profile"> <i class="fas fa-user-edit"></i> {{__('Edit profile')}}</a></div>
                    
                        @if($form->indexBottomMessage)<p class=" fainted-08"><small>{{$form->indexBottomMessage}}</small></p>@endif
                        @includeIf($form->getEditBottom())
                    
                             
                </div>
            </div>
        

    </div>
</section>
@endsection
