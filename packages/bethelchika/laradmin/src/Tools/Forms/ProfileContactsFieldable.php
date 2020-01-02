<?php
namespace BethelChika\Laradmin\Tools\Forms;

use BethelChika\Laradmin\Form\Contracts\Fieldable;
use BethelChika\Laradmin\Form\Field;
use BethelChika\Laradmin\Form\Group;
use Illuminate\Support\Facades\Auth;

/**
 * NOTE: UNUSED: This Fieldable class can be used to provide fields for address. But is not currently used in Laradmin.
 */

class ProfileContactsFieldable implements Fieldable{
/**
     * 
     *
     * @inheritdoc
     */
    public function handle($pack,$tag,Field $field){
        
        $user=Auth::user();
        switch($field->name){
            case 'country':
                $user->country=$field->value;
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
        // $country='';
        // if(Auth::user()->country){
        //     $country=__( 'laradmin::list_of_countries.'.Auth::user()->country );
        // }
        
        $f1=Field::make([
            'name'=>'country',
            'type'=>'select',
            'value'=>Auth::user()->country,
            'label'=>'Country',
            'order'=>5,
            'rules'=>'required|min:2',
            'options'=>__( 'laradmin::list_of_countries'),
        ]);
        
        
        return collect([$f1]);
        


    }

}