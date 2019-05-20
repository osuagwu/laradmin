<!-- list controls-->
<nav class="navbar navbar-default table-nav"   >
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar-collapse-{{$tableName}}" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-navbar-collapse-{{$tableName}}" >
            <ul class="nav navbar-nav">
            
                {{--Check if we need to include checkboc for selecting all rows --}}
                @if(isset($selectAllCheckbox) and $selectAllCheckbox==true)
                <li class="navbar-text hidden-xs hidden-sm">
                    @component('laradmin::blade_components.table_row_checkbox',['tableName'=>$tableName])
                    @endcomponent  
                </li>
                @endif
                
                
                {{--add others things--}}
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        @isset($actions['delete'])
                        <li>
                            <a onclick="jQuery('#delete_selected_{{$tableName}}_form').submit()">{{$actions['delete']['label']??'Delete selected'}}</a>
                            <form id="delete_selected_{{$tableName}}_form" class="hidden-xs-up" method="post" action="{{$actions['delete']['formAction']}}" onsubmit="var select_checkbox_temp=[];jQuery('td .{{$tableName}}-row-select-checkbox').each(function(){if($(this).prop('checked')){select_checkbox_temp.push($(this).val())}}); jQuery('#delete_selected_{{$tableName}}_form '+'input[name=\'{{$tableName}}_ids\']').val(select_checkbox_temp.join(','));select_checkbox_temp=[]; return confirm('You are about to delete the selected items!');">
                                {{ method_field('DELETE') }}
                                {{ csrf_field() }}
                                <input type="hidden" name="{{$tableName}}_ids" value="" />
                            </form>
                        </li>
                        @endisset
                        <li><a href="{{URL::current()}}">Clear Search</a></li>                                                    
                    </ul>
                </li>
                
            </ul>
            <form class="navbar-form navbar-left" method="get" action="{{request()->fullUrl()}}">
                <div class="form-group">
                <input type="hidden" name="search" value="1" />
                <input name="{{$tableName}}_search" value="{{old($tableName.'_search')}}" type="text" class="form-control input-sm" placeholder="Search">
                </div>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Search</button>
            </form>
                <p class="navbar-btn navbar-right">
                @if(isset($links))
                    @foreach($links as $link)
                    
                        
                            <a class="btn btn-sm btn-primary" href="{{$link['url']}}">
                                @if(isset($link['icon_class']))<i class="{{$link['icon_class']}}"></i> @endif
                                {{$link['label']}}
                            </a>
                        
                                
                    @endforeach  
                @endif 
                </p>              
            
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>