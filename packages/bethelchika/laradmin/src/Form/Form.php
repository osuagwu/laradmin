<?php

namespace BethelChika\Laradmin\Form;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use BethelChika\Laradmin\Form\Contracts\Fieldable;


 class Form{
     /**
     * The unique pack
     *
     * @var string
     */
    private $pack;
    /**
     * The unique tag
     *
     * @var string
     */
    private $tag;

     /**
      * Hold all form fields
      *
      * @var Collection
      */
     private $fields;


     /**
     * Name
     *
     * @var string
     */
    public $name;

     /**
      * Title of the form
      *
      * @var string
      */
     public $title='';

     /**
      * For description of index page
      *
      * @var string
      */
     public $indexDescription='';

     /**
      * For description of edit page
      *
      * @var string
      */
      public $editDescription='';

     /**
      * Message displayed at the footer of the form for index
      *
      * @var string
      */
     public $indexBottomMessage='';

     /**
      * Message displayed at the footer of the form for edit
      *
      * @var string
      */
      public $editBottomMessage='';

     /**
      * Construct a new form
      * @param string $pack       
      * @param string $tag A tag that uniquely identify this form
      * @param string $name
      */
     public function __construct($pack,$tag,$name=null){
        $this->pack=$pack;
         $this->tag=$tag;
         $this->fields=new Collection;

         $this->title=$pack.'/'. $tag;
         $this->name=$name;
         if(!$this->name){
             $this->name=$tag;
         }
     }

     /**
      * Gets the form  pack
      *
      * @return string
      */
      public function getPack(){
        return $this->pack;
    }

     /**
      * Gets the form  tag
      *
      * @return string
      */
     public function getTag(){
         return $this->tag;
     }
     /**
      * Gets the form  name
      *
      * @return string
      */
      public function getName(){
        return $this->name;
    }

    /**
      * Gets the form  title
      *
      * @return string
      */
      public function getTitle(){
        return $this->title;
    }

     /**
      * Add fields
      *
      * @param FormItem $field
      * @return void
      */
     public function addField(FormItem $field){
         $this->fields->push($field);
     }

     /**
      * Get all the fields registered for this form based on the form tag
      *
      * @return Collection
      */
     public function getRegisteredFields(){
        
        return FormManager::getRegisteredFields($this->pack,$this->tag);
     }


     /**
      * Get all fields
      * @param boolean $with_registered return also all the registered fields
      * @return Collection
      */
     public function getFields($with_registered=true){
         $fields=$this->fields;

         //Is this an form  a  Fieldable ?
         if($this instanceof  Fieldable ){
             $fields=$fields->merge($this->all($this->pack,$this->tag));
         }

         if($with_registered){
            $fields=$fields->merge($this->getRegisteredFields());
         }
         
         return $fields->sortBy('order')->values();
     }

     /**
      * Get all groups
      * @param boolean $with_registered return also all the registered groups when true
      * @return Collection
      */
     public function getGroups($with_registered=true){
        $fields=$this->getFields($with_registered);
        $fields=$fields->groupBy('group');

        if($fields->has(FormItem::GROUP_GROUP_NAME)){
            return $fields[FormItem::GROUP_GROUP_NAME];
        }
        return new Collection;
     }

       /**
      * Get a collection of groups(a collection) of field. The fields are grouped  in a collection of collection with each indexed by their group name.
      * @param boolean $with_registered return also all the registered groups when true
      * @return Collection
      */
      public function getGroupedFields($with_registered=true){
        $fields=$this->getFields($with_registered);
        return $fields->groupBy('group');

     }

          /**
      * Get a group
      * @param $name The name of the group to be retrieved
      * @param boolean $with_registered return also all the registered groups when true
      * @return Group
      */
      public function getGroup($name,$with_registered=true){
        $groups=$this->getGroups($with_registered);
        foreach($groups as $group){
            if(str_is($name,$group->name)){
                return $group;
            }
        }
        return null;
     }

     /**
      * Construct a validator for form
      *
      * @param array $inputs
      * @return Validator
      */
     public function getValidator($inputs){
        $fields=$this->getFields();
        
        $rules=array();
        $attributeNames=array();
        $messages=array();
        foreach($fields as $field){
            switch($field->type){
                case FormItem::GROUP:
                    break;
                case FormItem::FIELDSET:
                    foreach($field->getFields() as $fieldset_field){

                        //Rules
                        $rules[$fieldset_field->name]=$fieldset_field->rules;

                        //Attributes
                        $attributeNames[$fieldset_field->name]=$fieldset_field->label;

                        //Error Messages
                        if(isset($fieldset_field->messages)){
                            foreach($fieldset_field->messages as $key=>$message){
                                $messages[$fieldset_field->name.'.'.$key]=$message;
                            }   
                        }
                    }
                    break;
                default:

                    // rules
                    $rules[$field->name]=$field->rules;

                    //Attributes
                    $attributeNames[$field->name]=$field->label;

                    //Error Messages
                    if(isset($field->messages)){
                        foreach($field->messages as $key=>$message){
                            $messages[$field->name.'.'.$key]=$message;
                        }   
                    }
                    
            }
            
        }

        $validator = Validator::make($inputs,$rules,$messages);
        $validator->setAttributeNames($attributeNames);
   
        return $validator;
     }

     /**
      * Stores fields
      *
      * @param Request $request
      * @return void TODO: See if it is neccessary to return something from storeFieldableField(...) method
      */
     public function process(Request $request){
        $fieldables=FormManager::getFieldables($this->pack,$this->tag);

        //Is this an form  a  Fieldable ?
        if($this instanceof Fieldable ){
            $fieldables->push($this);
        }

        foreach($fieldables as $fieldable){
            $fields=$fieldable->all($this->pack,$this->tag);
            foreach($fields as $field){
                switch($field->type){
                    case FormItem::GROUP:
                        break;
                    case FormItem::FIELDSET:
                        foreach($field->getFields() as $fieldset_field){
                            // if($request->has($fieldset_field['name']) and strcmp($fieldset_field['name'],$request->input($fieldset_field['name']))){
                            //     $fieldset_field['value']=$request->input($fieldset_field['name']);
                            //     $fieldable->store($this->tag,$fieldset_field);
                            // }
                            $this->storeFieldableField($fieldable,$fieldset_field,$request);
                        }
                        break;
                    default:
                        // if($request->has($field->name) and strcmp($field->name,$request->input($field->name))){
                        //     $field->value=$request->input($field->name);
                        //     $fieldable->store($this->tag,$field);
                        // }
                        $this->storeFieldableField($fieldable,$field,$request);
                
                }
                
            }
        }
     }
     /**
      * Call handles of fieldables
      *
      * @param Fieldable $fieldable
      * @param string $tag
      * @param Field $field
      * @param Request $request
      * @return int Returns 0=>Fail, 1=>success, -1=>unchanged/unknown field
      */
     private function storeFieldableField(Fieldable $fieldable,Field $field,Request $request){
        if($request->has($field->name) and strcmp($field->name,$request->input($field->name))){
            $field->value=$request->input($field->name);
           return $fieldable->handle($this->pack,$this->tag,$field);
        }
        return -1;
     }

     

     /**
      * Returns a view file for the top of index page
      *
      * @return string
      */
     public function getIndexTop(){
        return null;
     }

     /**
      * Returns a view file for the bottom of index page
      *
      * @return string
      */
     public function getIndexBottom(){
        return null;
    }

    /**
      * Returns a view file for the top of edit page
      *
      * @return string
      */
    public function getEditTop(){
        return null;
    }

    /**
      * Returns a view file for the bottom of edit page
      *
      * @return string
      */
    public function getEditBottom(){
        return null;
   }

   /**
    * Return form manager
    *
    * @return \BethelChika\Laradmin\Form\FormManager
    */
  public static function manager(){
      return app('laradmin')->formManager;
  }
     
 }