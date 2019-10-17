<?php
namespace BethelChika\Laradmin\Tools\Forms;

use BethelChika\Laradmin\Form\Contracts\Fieldable;
use BethelChika\Laradmin\Form\Field;
use BethelChika\Laradmin\Form\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class CPSettingsFieldable implements Fieldable{
/**
     * 
     *
     * @inheritdoc
     */
    public function handle($pack,$tag,Field $field){
        
        return 1;
    }



      /**
     * 
     *
     * @inheritdoc
     */
    public function all($pack,$tag,$mode){
        //
        $fields=new Collection();
        

        switch($tag){
        
            case 'general':
                
                $fields->push(Group::make([
                    'name'=>'config',
                    'label'=>'Laradmin configuration file',
                    'order'=>2,
                    'indexDescription'=>'These configurations can only be edited through the config file and the Laravel .env file.'
                ]));

                foreach(config('laradmin') as $k=>$v){
                    
                    if(is_array($v)){
                        $v_temp='';
                        foreach($v as $k2=>$v2){
                            if(is_array($v2)){
                                $v_temp=$v_temp.'; '.$k2.':Array (see config for details)';
                            }else{
                                $v_temp=$v_temp.'; '.$k2.':'.$v2;
                            }
                            
                        }
                        $v=trim($v_temp,';');
                    }
                    $fields->push(
                        Field::make([
                            'name' => $k,
                            'value' => $v,
                            'label' => $k,
                            'group' => 'config',
                            'order' => 0,
                            'isReadOnly'=>true,
                            
                        ])
                    );
                }
                
                break;
            default:
        }

        
        return $fields;


    }

}