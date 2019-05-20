<?php

namespace BethelChika\Laradmin\Http\Controllers\User;


use BethelChika\Laradmin\User;
use Illuminate\Http\Request;

use BethelChika\Laradmin\Http\Controllers\Controller;
use BethelChika\Laradmin\Traits\ReAuthController;
use BethelChika\Laradmin\Laradmin;

use BethelChika\Laradmin\Form\Form;
class AutoformController extends Controller
{


    private $laradmin;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Laradmin $laradmin)
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware('re-auth:30')->only(['edit']);

        $this->laradmin=$laradmin;
        //$this->laradmin->contentManager->loadMenu('user_settings');
        $this->laradmin->assetManager->registerMainNavScheme('primary');
        $this->laradmin->assetManager->setContainerType('fluid');
      
    }
    public function index(Request $request,$pack,$tag){
        $form=$this->laradmin->formManager->getAutoform($pack,$tag,'index');
        if(!$form){
            return abort(404);
        }
        if(!$form->gate($request->user())){
            return abort(403);
        }
        $form->build();

       



        if (method_exists($form,'index')){
            return $form->index($pack,$tag);//TODO:Reverse $pack and $tag in aff form system
        }
        
        
        
        $pageTitle=$form->title;
        return view('laradmin::form.autoform.index',compact('form','pageTitle'));
    }

    public function edit(Request $request,$pack,$tag){
        $form=$this->laradmin->formManager->getAutoform($pack,$tag,'edit');
        if(!$form){
            return abort(404);
        }
        
        
        if(!$form->gate($request->user())){
            return abort(403);
        }
        $form->build();
        //$fields=$form->getGroupedFields();
        
        
        $pageTitle=$form->title;
        return view('laradmin::form.autoform.edit',compact('form','pageTitle'));
    }
    
    public function process(Request $request,$pack,$tag){
        $form=$this->laradmin->formManager->getAutoform($pack,$tag,'edit');
        if(!$form){
            return abort(404);
        }
        if(!$form->gate($request->user())){
            return abort(403);
        }
        $form->build();
        $form->getValidator($request->all())->validate();
        $form->process($request);
        return redirect()->route('user-autoform',[$pack,$tag])->with('success','Done');
    }
}