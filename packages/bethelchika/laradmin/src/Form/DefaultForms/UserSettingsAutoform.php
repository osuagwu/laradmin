<?php
namespace BethelChika\Laradmin\Form\DefaultForms;
use BethelChika\Laradmin\User;
use BethelChika\Laradmin\Form\Field;
use BethelChika\Laradmin\Form\FormItem;
use BethelChika\Laradmin\Form\Contracts\AutoForm;
use BethelChika\Laradmin\Form\Group;

class UserSettingsAutoform extends Autoform{
     /**
     * @inheritdoc
     */
    function gate(User $user){
        return true;
    }
    /**
     * 
     *
     * @inheritdoc
     */
    public function handle($pack,$tag,Field $field){
        
        //print_r($field);
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
    function build(){
        $this->title="Test for Autoform";
        $field=new Field('author_name');
        $field->type='text';
        $field->label='Lara name';
        $field->value='BC';
        $field->rules='required|min:2';
        $field->order=0;
        
        $this->addField($field);
    }
   

    /**
     * 
     *
     * @inheritdoc
     */
    function all($pack,$tag=true){
        

      //Fields  

      $group1=new Group('personal');
      $group1->lable="Personal";
      $group1->editDescription=$group1->indexDescription="Personal data information";
      
      


        $field1=FormItem::make([   'type'=>'text',
        'name'=>'screen_name',
        'label'=>'Sname',
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

        $field2=new Field('laradmin_author_name');
        $field2->type='text';
        $field2->label='Laradmin author name';
        $field2->value='BC';
        $field2->rules='required|min:3';
        $field2->order=1;
        
        //$this->addField($field2);
        return collect([$group1,$field1,$field2]);
    }

}