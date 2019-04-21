<?php
namespace BethelChika\Laradmin\Tools\Forms;

use BethelChika\Laradmin\Form\Contracts\Fieldable;
use BethelChika\Laradmin\Form\Field;
use BethelChika\Laradmin\Form\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class ProfileFieldable implements Fieldable{
/**
     * 
     *
     * @inheritdoc
     */
    public function handle($pack,$tag,Field $field){
        
        //dd($field);
        //dd(\Auth::user());
        return 1;
    }



      /**
     * 
     *
     * @inheritdoc
     */
    public function all($pack,$tag,$mode){

        $fields=new Collection();
        $fields->push(Group::make([
            'name'=>'screen name',
            'label'=>'Screen name',
            'order'=>2,
        ]));

        switch($tag){
        
            case 'personal':
                $fields->push(
                    Group::make([
                        'name' => 'names',
                        'label' => 'Names',
                        'order' => 2,
                    ])
                );
                $fields->push(
                    Field::make([
                        'name' => 'name',
                        'value' => Auth::user()->name,
                        'label' => 'Screen name',
                        'group' => 'names',
                        'order' => 0,
                        'rules' => 'required:min:2',
                    ])
                );
                if(str_is($mode,'index')){
                    $fields->push(
                        Field::make([
                            'name' => 'email',
                            //'type'=>Field::HTML,
                            'value' => Auth::user()->email,// .'<p><a class="fainted-04" href="'.route('social-user-link-email').'"  > <i class="fas fa-pen"></i></a></p>',
                            'editLink'=>route('social-user-link-email'),
                            'label' => 'Primary email',
                            'group' => 'emails',
                            'order' => 0,
                            'rules' => 'required:min:2',
                        ])
                    );
                    $fields->push(
                        Group::make([
                            'name' => 'emails',
                            'label' => 'Emails',
                            'order' => 3,
                        ])
                    );
                }
                break;
            default:
        }

        
        return $fields;


    }

}