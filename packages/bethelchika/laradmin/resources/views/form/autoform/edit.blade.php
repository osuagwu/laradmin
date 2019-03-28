
@extends('laradmin::user.layouts.app')

@section('content')
<section class="section section-first section-full-page section-info section-last">
    <div class="container">
        
        <div class="row">
            <div class="col-md-2 padding-top-x10">
                <a class="heading-1 text-white" href="{{route('user-autoform',[$form->getPack(),$form->getTag()])}}" title="Back">
                    <span class="iconify " data-icon="entypo-chevron-thin-left" data-inline="false"></span>
                    <noscript><i class="fas fa-chevron-left"></i></noscript>
                </a>
            </div>
            <div class="col-md-8   ">
                    <h1 class="heading-1 content-title">{{$pageTitle??'Edit '}}</h1>
                    @if($form->editDescription)<p class=" fainted-08"><small>{{$form->editDescription}}</small></p>@endif
                    @includeIf($form->getEditTop())
                @include ('laradmin::inc.msg_board')
                @include('laradmin::inc.email_confirmation_prompt')


                <form class="form-horizontal" role="form" method="POST" action="{{route('user-autoform-edit',[$form->getPack(),$form->getTag()])}}">
                    {{ method_field('PUT') }}
                    {{ csrf_field() }}
 
                    @foreach($form->getGroupedFields() as $group_name=> $fields)
                        @if(str_is($group_name,'__group__'))
                            @continue
                        @endif
                        <div class="group">
                            @if(!str_is($group_name,'__') and $form->getGroup($group_name))
                                <h6 class="label label-warning ">{{$form->getGroup($group_name)->label??ucfirst($group_name)}}</h6>
                                {{-- @if($form->getGroup($group_name)->editDescription) --}}
                                    <span class="description">{{$form->getGroup($group_name)->editDescription}}</span>
                                {{-- @endif --}}
                                <hr class=" list-separator">
                            @endif
                            @component('laradmin::form.fields',['fields'=>$fields])
                            @endcomponent
                        </div>
                    @endforeach
                    
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            
                            <a class="btn btn-subtle" href="{{route('user-autoform',[$form->getPack(),$form->getTag()])}}">
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
    </div>
</section>
@endsection


