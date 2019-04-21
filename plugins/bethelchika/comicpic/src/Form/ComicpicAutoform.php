<?php
namespace BethelChika\Comicpic\Form;
use BethelChika\Laradmin\User;
use BethelChika\Laradmin\Form\Field;
use BethelChika\Laradmin\Form\FormItem;
use BethelChika\Laradmin\Form\Contracts\AutoForm;
use BethelChika\Laradmin\Form\Group;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;


class ComicpicAutoform extends Autoform{
     /**
     * @inheritdoc
     */
    function gate(User $user){
        return Auth::user()->id==$user->id;
    }
    /**
     * 
     *
     * @inheritdoc
     */
    public function handle($pack,$tag,Field $field){
        
        //print_r($field);
        //dd(\Auth::user());
        switch($field->name){
            case 'comicpic_avatar':
                //dd($field);
                break;
        }
        return 1;
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
    public function all($pack,$tag,$mode){
        $field=FormItem::make([   'type'=>'select',
        'name'=>'comicpic_auto_feeds',
        'label'=>'Automatically post a feed',
        //'group'=>'comipic_settings',
        'order'=>0,
        'help'=>'Do you want this item to be posted to feed when published?',
        'placeholder'=>'Please choose',
        'class'=>'',
        'unit'=>'',
        'value'=>'yes',
        'options'=>['yes'=>'Yes','no'=>'No'],
        'rules' => 'required',
        'messages'=>['required'=>'Please specify if you want automatic feeds when you publish',
                    ]
        ]);
        $f0=Field::make([
            'name'=>'comicpic_avatar',
            'type'=>Field::IMAGE,
            'value'=>Auth::user()->avatar,
            'label'=>'Comicpic avatar',
            'group'=>'comicpic_author',
            'order'=>9,
            'rules'=>'nullable|image|mimes:jpg,jpeg,png,bmp,gif|max:1000',
        ]);
        $group1=Group::make(['name'=>'comicpic_author','label'=>'Comicpic author','order'=>-10]);
        
        return collect([$group1,$field,$f0]);

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
            $this->name="Replicated form just for testing autoform menus";
        }
        return $this->name;
    }

    

}