<?php
namespace BethelChika\Comicpic\Form;

use BethelChika\Laradmin\Form\Contracts\Fieldable;
use BethelChika\Laradmin\Form\Field;
use BethelChika\Laradmin\Form\FormItem;

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

    public function show($pack,$tag,Field $field){

    }

      /**
     * 
     *
     * @inheritdoc
     */
    public function all($pack,$tag){
        $field1=FormItem::make([   'type'=>'text',
        'name'=>'comicpic_author_screen_name',
        'label'=>'Comicpic author screen name',
        'group'=>'personal',
        'order'=>0,
        'help'=>'Help text',
        'placeholder'=>'Enter text',
        'class'=>'comicpic-field1',
        'unit'=>'(£)',
        'value'=>'Bethel',
        'options'=>[],
        'rules' => 'required|min:5',
        'messages'=>['required'=>'Comicpic author screen name must be given',
                        'min'=>'Comicpic author screen name cannot be less than five characters',
                    ]

        ]);

        $field2=new Field('comipic_author_name');
        $field2->type='password';
        $field2->label='Comicpic author name';
        $field2->value='BC';
        $field2->rules='required|min:3';
        $field2->order=0;
        
        return collect([$field1,$field2]);


    }

}