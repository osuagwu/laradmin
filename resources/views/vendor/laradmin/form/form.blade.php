
<div class="form-page index">
    <div class="header ">
        {{-- <h1>{{ucfirst(str_replace('_',' ',$form->getPack()))}}</h1>
        <nav class="nav nav-tabs nav-flat">
            @include('laradmin::menu',['tag'=>$form->formPackToMenu()])
        </nav> --}}
        
            
        {{-- <h1 class="heading-2 page-title">{{$pageTitle}}</h1>  --}}
       
    </div>
    
    
  

    
            
    @if($form->indexDescription)<p class=" fainted-08"><small>{{$form->indexDescription}}</small></p>@endif
    @includeIf($form->getIndexTop())   
    

    @foreach($form->getGroupedFields() as $group_name=> $fields)
        <div class="group">
            @if($form->getGroup($group_name))
                <h3 class="heading-6 heading-slashes">{{$form->getGroup($group_name)->label??ucfirst($group_name)}}</h3>
                {{-- @if($form->getGroup($group_name)->editDescription) --}}
                    <span class="description">{{$form->getGroup($group_name)->indexDescription}}</span>
                {{-- @endif --}}
                
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

    @if($form->getEditLink())<div class="text-right padding-top-x2"><a class="btn btn-primary btn-xs" href="{{$form->getEditLink()}}" title="Edit"> <i class="fas fa-edit"></i> {{__('Edit')}}</a></div>@endif

    @if($form->indexBottomMessage)<p class=" fainted-08"><small>{{$form->indexBottomMessage}}</small></p>@endif
    @includeIf($form->getEditBottom())
</div>

