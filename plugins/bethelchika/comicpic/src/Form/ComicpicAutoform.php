<?php
namespace BethelChika\Comicpic\Form;
use BethelChika\Laradmin\User;
use BethelChika\Laradmin\Form\Field;
use BethelChika\Laradmin\Form\FormItem;
use BethelChika\Laradmin\Form\Contracts\AutoForm;
use BethelChika\Laradmin\Form\Group;
use Illuminate\Support\Collection;


class ComicpicAutoform extends Autoform{
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
        $this->navCloseRoute='comicpic.user_settings';

        $this->getTitle();
        $this->getName();
        
        return new Collection;
    }
   

     /**
     * 
     *
     * @inheritdoc
     */
    public function all($pack,$tag){
        $field=FormItem::make([   'type'=>'text',
        'name'=>'comicpic_auto_feeds',
        'label'=>'Automatically post a feed',
        'group'=>'comipic_settings',
        'order'=>0,
        'help'=>'Do you want this item to be posted to feed when published?',
        'placeholder'=>'Please choose',
        'class'=>'',
        'unit'=>'',
        'value'=>'yes',
        'options'=>['yes','no'],
        'rules' => 'required',
        'messages'=>['required'=>'Please specify if you want automatic feeds when you publish',
                    ]
        ]);
        
        
        return collect([$field]);


    }

    /*********Lets overide Some parent methods ******** */
    
    /**
     * @inheritDoc
     *
     */
    public function getTitle(){
       return $this->title="Automatic form for comicpic";
        
    }

    /**
     * @inheritDoc
     *
     */
    public function getName(){
        $this->name="Automatic feeds";
        
        if(str_is($this->getTag(),"user_settings2")){
            $this->name="Test form";
        }
        return $this->name;
    }

    

}