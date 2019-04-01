<?php
namespace BethelChika\Laradmin\Form;
abstract class FormItem{
    /**
     * Field type
     *
     * @var string
     */
    public const TEXT='text';

    /**
     * Field type
     *
     * @var string
     */
    public const PASSWORD='password';

        /**
     * Field type
     *
     * @var string
     */
    public const HIDDEN='hidden';

    /**
     * Field type
     *
     * @var string
     */
    public const SELECT='select';

    /**
     * Field type
     *
     * @var string
     */
    public const CHECKBOX='checkbox';

    /**
     * Field type
     *
     * @var string
     */
    public const RADIO='radio';

    /**
     * Field type
     *
     * @var string
     */
    public const TEXTAREA='textarea';

    /**
     * Field type
     *
     * @var string
     */
    public const FIELDSET='fieldset';

     /**
     * Field type
     *
     * @var string
     */
    public const GROUP='group';
    ///////////////////////////////////////////////////////////

     /**
     * The group name of Group field type.
     *
     * @var string
     */
    public const GROUP_GROUP_NAME='__group__';
    //////////////////////////////////////////////////////////

    /**
     * The type of field
     * e.g: FORMFIELD::TEXT
     * @var string
     */
    public $type;

    /**
     * Name of the form field
     *
     * @var string
     */
    public $name;

    /**
     * The label of the form field
     *
     * @var string
     */
    public $label;

    /**
     * Name of groups this field belongs to
     *
     * @var string
     */
    public $group='__';

    /**
     * The order of the field i relation to the fields in a form
     *
     * @var int
     */
    public $order;

    /**
     * COnstruct a new item from array; the item returned can be a field or fieldset based on the type array index
     *
     * @param array $props Must contain 'name' index if the item to be returned is a Field
     * @return Field|Fieldset The created item oy false;
     */
    public static function make($props){

        //Lets first get the name
        $name=null;
        foreach ($props as $prop=>$v){
            if(str_is($prop,'name')){
                $name=$v;
                break;
            }
        }

        if(!$name)return false;// We cannot do without a name

        $item=null;
        switch($props['type']){
            case FormItem::FIELDSET:
                $item=new Fieldset($name);
                break;
            default:
                $item=new Field($name);
                
        }
        
        
        foreach ($props as $prop=>$v){
            if(str_is($prop,'name')){
                continue;
            }
            $item->$prop=$v;
        }

        return $item;
    }


}