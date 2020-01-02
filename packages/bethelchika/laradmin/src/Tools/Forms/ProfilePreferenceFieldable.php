<?php
namespace BethelChika\Laradmin\Tools\Forms;

use BethelChika\Laradmin\Form\Contracts\Fieldable;
use BethelChika\Laradmin\Form\Field;
use BethelChika\Laradmin\Form\Group;
use BethelChika\Laradmin\Tools\Tools;
use Illuminate\Support\Facades\Auth;

class ProfilePreferenceFieldable implements Fieldable{
/**
     * 
     *
     * @inheritdoc
     */
    public function handle($pack,$tag,Field $field){
        
        $user=Auth::user();
        $has_changed=false;
        switch($field->name){
            case 'local':
                $user->local=$field->value;
                $has_changed=true;
            case 'timezone':
                $user->timezone=$field->value;
                $has_changed=true;
                
        }
        if($has_changed){
            $user->save();
        }
        
        
        return 1;
    }

  

      /**
     * 
     *
     * @inheritdoc
     */
    public function all($pack,$tag,$mode){

        $g1=Group::make([
            'name' => 'general',
            'label' => 'General',
            'order' => 1,
        ]);

        $f1=Field::make([
            'name'=>'local',
            'type'=>'select',
            'value'=>Auth::user()->local,
            'label'=>'Local/Language',
            'order'=>1,
            'group'=>'general',
            'rules'=>'nullable|min:2',
            'options'=>__( 'laradmin::list_of_locals'),
        ]);

        $f2=Field::make([
            'name'=>'timezone',
            'type'=>'select',
            'value'=>Auth::user()->timezone,
            'label'=>'Timezone',
            'order'=>1,
            'group'=>'general',
            'rules'=>'nullable|min:2',
            'options'=>Tools::getTimezones(),
        ]);

        
        
        
        return collect([$g1,$f1,$f2]);
        


    }

}