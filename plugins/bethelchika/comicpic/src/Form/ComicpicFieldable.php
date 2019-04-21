<?php
namespace BethelChika\Comicpic\Form;

use BethelChika\Laradmin\Form\Contracts\Fieldable;
use BethelChika\Laradmin\Form\Field;
use BethelChika\Laradmin\Form\Group;
use Illuminate\Support\Facades\Auth;
use BethelChika\Laradmin\Form\Fieldset;

class ComicpicFieldable implements Fieldable{
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
        $group1=Group::make(['name'=>'comicpic_author','label'=>'Comicpic author','order'=>10]);
        $field1=Field::make([   'type'=>Field::TEXT,
        'name'=>'comicpic_screen_name',
        'label'=>'Author name',
        'group'=>'comicpic_author',
        'order'=>0,
        'help'=>'Help text',
        'placeholder'=>'Enter author name',
        'class'=>'',
        'unit'=>'',
        'value'=>'',
        'options'=>[],
        'rules' => 'required|min:2',
        'messages'=>['required'=>'Comicpic author screen name must be given',
                        'min'=>'Comicpic author screen name cannot be less than 2 characters',
                    ]

        ]);


        
        
        return collect([$group1,$field1]);


    }

}