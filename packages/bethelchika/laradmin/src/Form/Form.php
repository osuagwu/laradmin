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
     * Tells what mode the form is opened.
     * values are:{
     * 'index'=>readonly for index page,
     * 'edit'=>read/write for edit page
     * }
     *
     * @var string
     */
    private $mode; 
    
    
    /**
      * Hold all form fields
      *
      * @var Collection
      */
     private $fields;

     /**
      * The method of the form eg: 'GET','POST' 'DELETE', ...
      *
      * @var string
      */
     public $method='PUT';//

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
      * The url to the form index page
      *
      * @var string
      */
     public $link=null;

     /**
      * The url to the form edit page
      *
      * @var string
      */
     public $editLink=null;


    /**
      * The form action url
      *
      * @var string
      */
      public $action=null;

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
       * @param string $mode Tells what mode the form should be opened: {'index','edit'}
      * @param string $name
      */
     public function __construct($pack,$tag,$mode,$name=null){
        $this->pack=$pack;
         $this->tag=$tag;
         $this->mode=$mode;
         $this->fields=new Collection;

         $this->title=$pack.'/'. $tag;
         $this->name=$name;
         if(!$this->name){
             $this->name=$tag;
         }


         ////////////////Start unneccessary code///////////////////////////////////////////////////////
         ///////CAUTION:FIXME:  Note that this is unneccessary and can safely be deleted//////////////
         // Define groups for fields that have default group property: 
        //  $this->addField(Group::make([
        //      'name'=>GROUP::DEFAULT_GROUP_NAME,
        //      'label'=>'__Unknown__',
        //      'order'=>1000,//Any large number to make it come last
        //      ]));
        /////////////////////////End uneccessary code///////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////
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
      * Add field /group/fieldset
      *
      * @param FormItem $field Any Formitem not just Field
      * @return void
      */
     public function addField(FormItem $field){
         $this->fields->push($field);
     }

     /**
      * Get all the fields registered for this form based on the form tag
      * @return Collection
      */
     public function getRegisteredFields(){
        
        return FormManager::getRegisteredFields($this->pack,$this->tag,$this->mode);
     }


     /**
      * Get all fields
      * @param boolean $with_registered return also all the registered fields
      * @return Collection
      */
     public function getFields($with_registered=true){
         $fields=$this->fields;

         //Is this form  a  Fieldable ?
         if($this instanceof  Fieldable ){
             $fields=$fields->merge($this->all($this->pack,$this->tag,$this->mode));//NOTE: In this was Autoform which are fieldables can be included in the list of fieldables, so no need to implement the getField() function for Autoforms
         }

         if($with_registered){
            $fields=$fields->merge($this->getRegisteredFields());
         }
         
         return $fields->sortBy('order')->values();
     }

     /**
      * Check if form has fields
      *
      * @param boolean $with_registered
      * @return boolean
      */
     public function isEmpty($with_registered=true){
         if(count($this->getFields(false))){
             return false;
         }
         if($with_registered){
            return !count($this->getFields($with_registered));
         }
        
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
            //dd($fields[FormItem::GROUP_GROUP_NAME]->sortBy('order')->values());
            return $fields[FormItem::GROUP_GROUP_NAME]->sortBy('order')->values();
        }
        return new Collection;
     }

       /**
      * Get a collection of ordered groups(a collection) of field. The fields are grouped  in a collection of collection with each indexed by their group name.
      * @param boolean $with_registered return also all the registered groups when true
      * @return Collection
      */
      public function getGroupedFields($with_registered=true){
        $fields=$this->getFields($with_registered);
        
        $groups=$fields->groupBy('group');
        
        //Do not return the Group group
         $groups->forget(Group::GROUP_GROUP_NAME);

         //Now we will order the groups

         
        foreach($groups as $group_name=>$group){
            $g=$this->getGroup($group_name,$with_registered);
            if($g){
                $order=$g->order;
            }else{
                $order=$group->min('order');// Use the order of the filed with list order;
            }
            $group->put('order',$order);//attach order to the outer collection, we will delete later
                
        }
         // Now sort with the attached order and remove it.
         $groups=$groups->sortBy('order');//

         foreach($groups as $group){
             $group->forget('order');
         }
         return $groups;
         
        // $group_names=new Collection();
        // foreach($groups->keys() as $group_name){
        //     $g=$this->getGroup($group_name,$with_registered);
        //     if($g){
        //         $order=$g->order;
        //     }else{
        //         $order=2000;//Something very large to make undefined group order later than default group 
        //     }
        //     $group_names->push(['name'=>$group_name,'order'=>$order]);  
        // }
        // $ordered_groups=new Collection();
        // foreach($group_names->sortBy('order') as $group_name){
        //     $ordered_groups->put($group_name['name'],$groups[$group_name['name']]);
        // }
        // return $ordered_groups;
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
            $fieldables->push($this); // This makes sure that Autoforms are also processed as fieldables which they are. TODO: but it will be nice to do this outside the Form so that form does not have to think about it
        }

        foreach($fieldables as $fieldable){
            $fields=$fieldable->all($this->pack,$this->tag,$this->mode);
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
      * Call handle of a fieldable
      *
      * @param Fieldable $fieldable
      * @param string $tag
      * @param Field $field
      * @param Request $request
      * @return int Returns 0=>Fail, 1=>success, -1=>unchanged/unknown field
      */
     private function storeFieldableField(Fieldable $fieldable,Field $field,Request $request){
         
        
            
        if($request->has($field->name) ){//and strcmp($field->value,$request->input($field->name))){
            
            if(!strcmp($field->type,Field::IMAGE)){
                $field->value=$request->{$field->name};// All field types can be retrieved this way avoiding the else statement below, but we will leave it just to emphasize that image field type will not work like in the else statement
            }else{
                $field->value=$request->input($field->name);
            }
           return $fieldable->handle($this->pack,$this->tag,$field);
        }
        return -1;
     }

     /**
     * Returns the route to go when form is closed
     *
     * @return string
     */
    public function getNavCloseLink(){
        return $this->getLink();
    }

    /**
     * Gets the url for form
     *
     * @return string
     */
    public function getLink(){
        return $this->link;
    }
    

    /**
     * Gets the url for editing form
     *
     * @return string
     */
    public function getEditLink(){
        return $this->editLink;
    }

     /**
      * Make a navigation using all the form in the specified pack and return the menu tag
      * @param string $base_url The base url of the for the pack such that each form can be reached thus: $base_url/pack/{tag}
      * @param string $parent_tag Tag/dot.separeted tag to a navigation item that the new natigation should be a child of. If not given a new menu is created instead. 
      * @return string
      */
     public function packToMenu($base_url,$parent_tag=null){
         $pack=$this->pack;
        $forms_nav_tag='__form_'.$pack.'__';
        if($parent_tag){
            $forms_nav_tag=$parent_tag;
        }
        // Create a menu for user settings forms from registered filedables
        $fieldables=$this->manager()->getFieldableNames($pack);
          foreach($fieldables as $f_tag=>$fieldable){
                $pack_tag=$pack.$f_tag;
                $route=$base_url.'/'.$pack.'/'.$f_tag;
                self::manager()->navigation()->create(ucfirst(str_replace('_',' ',$f_tag)),$pack_tag,$forms_nav_tag,[
                'url'=>$route,'iconClass'=>'fab fa-wpforms']);
          }
          return  $forms_nav_tag;
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
    * Check if a given set of fields contain one of type IMAGE
    *
    * @param Collection $fields
    * @return boolean
    */
   public static function hasImageField(Collection $fields){
        foreach($fields as $field){
            switch($field->type){
                case FormItem::IMAGE:
                    return true;
                    break;
                case FormItem::FIELDSET:
                    if(self::hasImageField($field->getFields())){
                        return true;
                    }
                    break;
                default:
            }
        }
        return false;          

   }
/**
   * CHeck if a filed is image
   *
   * @param Field $field
   * @return boolean
   */
  public static function isImageField(Field $field){
      return str_is($field->type,Field::IMAGE);
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