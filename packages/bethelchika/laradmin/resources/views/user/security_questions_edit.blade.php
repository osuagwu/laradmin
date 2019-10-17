@extends('laradmin::user.layouts.app')
@include('laradmin::user.partials.content_manager.stacks')
@section('content')
<section class="section section-primary section-first">
    <div class="container-fluid">
        @include('laradmin::user.partials.minor_nav',['left_menu_tag'=>'user_settings','scheme'=>'primary','title'=>'User settings','root_tag'=>false])
    </div>
</section>

<section class="section section-subtle section-light-bg section-diffuse section-diffuse-no-shadow">
    <div class="container-fluid">
        <div class="sidebar-mainbar">
            {{-- sidebar control --}}
            @include('laradmin::user.partials.sidebar.init') 
            <aside class="sidebar"  role="presentation">
                
                <div class="sidebar-content mCustomScrollbar" data-mcs-theme="minimal-dark">
                    <div class="sidebar-close-btn" title="Close sidebar">X</div>
                    <div class="back-to-usettings"><a href="{{route('user-settings')}}" title="Back to main settings" ><i class="fas fa-chevron-circle-left"></i> Back to main settings</a></div>
                    {{-- sidebar content --}}
                    @include('laradmin::user.partials.quick_settings')
                    
                </div>
            </aside>
    
            <!-- Page Content Holder -->
            <div class="mainbar" role="main">
                <div class="row">
                    <div class="col-md-12">    
                        @include('laradmin::menu.breadcrumb')

                        <div class="heading-huge">Manage your account access and security settings</div>
                        @include ('laradmin::inc.msg_board')
                        @include('laradmin::inc.email_confirmation_prompt')
                        <nav class="nav nav-tabs nav-flat">
                                @include('laradmin::menu',['tag'=>$laradmin->navigation->getMinorNavTag()])
                        </nav>

                        

                        <h3  class="heading-3" ><a class="" href="{{route('user-security-questions')}}">
                                <i class="fas fa-arrow-left fainted-05"></i>
                            </a> Security questions</h3>
                        <p class="padding-bottom-3 fainted-05">
                            Your previous answers have been hidden for security reasons. You can go ahead and change them. You should answer all questions.
                        </p>
                        <form class="form-horizontal" role="form" method="POST" action="{{route('user-security-questions-edit')}}" autocomplete="off">
                            @if(count($answers_values))
                                @method('PUT')
                            @endif
                            {{ csrf_field() }}
                            @if(count($answers_values))
                                @foreach($answers_values as $a_v_key=>$a_v)
                                    @component('laradmin::form.components.input_select',['name'=>'security_questions['.$loop->index.']','value'=>$a_v_key,'options'=>$questions_options,'label'=>'Question '.($loop->index+1),'old_name'=>'security_questions.'.$loop->index])
                                    @endcomponent
                                    @component('laradmin::form.components.input_text',['name'=>'security_answers['.$loop->index.']','value'=>'','label'=>'Answer to Question '.($loop->index+1),'required'=>'required','placeholder'=>'*******','old_name'=>'security_answers.'.$loop->index])
                                    @endcomponent 
                                    @component('laradmin::form.components.input_text',['name'=>'security_answer_reminders['.$loop->index.']','value'=>$a_v['reminder'],'label'=>'Answer reminder for Question '.($loop->index+1),'help'=>'Make sure the reminder does not contain the answer','old_name'=>'security_answer_reminders.'.$loop->index])
                                    @endcomponent
                                    <hr>
                                @endforeach
                            @else
                                @for($i=1;$i<=$security_answers_count;$i++)
                                    @component('laradmin::form.components.input_select',['name'=>'security_questions['.($i-1).']','value'=>'','options'=>$questions_options,'label'=>'Question '.($i),'old_name'=>'security_questions.'.($i-1)])
                                    @endcomponent
                                    @component('laradmin::form.components.input_text',['name'=>'security_answers['.($i-1).']','value'=>'','label'=>'Answer to Question '.($i),'required'=>'required','old_name'=>'security_answers.'.($i-1)])
                                    @endcomponent 
                                    @component('laradmin::form.components.input_text',['name'=>'security_answer_reminders['.($i-1).']','value'=>'','label'=>'Answer reminder for Question '.($i),'help'=>'Make sure the remider does not contain the answer','old_name'=>'security_answer_reminders.'.($i-1)])
                                    @endcomponent
                                    <hr>
                                @endfor
                            @endif
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    
                                    <a class="btn btn-subtle" href="{{route('user-security')}}">
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
        </div>

    </div>
</section>
@endsection
