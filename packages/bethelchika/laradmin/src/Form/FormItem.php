<?php
namespace BethelChika\Laradmin\Form;

abstract class FormItem
{
    /**
     * Field type
     *
     * @var string
     */
    public const TEXT = 'text';

    /**
     * Field type
     *
     * @var string
     */
    public const PASSWORD = 'password';

    /**
     * Field type
     *
     * @var string
     */
    public const HIDDEN = 'hidden';

    /**
     * Field type
     *
     * @var string
     */
    public const SELECT = 'select';

    /**
     * Field type
     *
     * @var string
     */
    public const CHECKBOX = 'checkbox';

    /**
     * Field type
     *
     * @var string
     */
    public const RADIO = 'radio';

    /**
     * Field type
     *
     * @var string
     */
    public const TEXTAREA = 'textarea';

     

    /**
     * Field type
     *
     * @var string
     */
    public const IMAGE = 'image';

    /**
     * Field type for displaying rich text
     *
     * @var string
     */
    public const HTML = 'html';

    /**
     * Field type
     *
     * @var string
     */
    public const FIELDSET = 'fieldset';

    /**
     * Field type
     *
     * @var string
     */
    public const GROUP = 'group';
    ///////////////////////////////////////////////////////////

    /**
     * The group name of Group field type.
     *
     * @var string
     */
    public const GROUP_GROUP_NAME = '__group__';

     /**
     * The group name of am item that does not have group property spicified
     *
     * @var string
     */
    public const DEFAULT_GROUP_NAME = '__';
    //////////////////////////////////////////////////////////

    /**
     * The type of field
     * e.g: FORMFIELD::TEXT
     * @var string
     */
    public $type;

    /**
     * Id of the form field
     *
     * @var string
     */
    public $id;

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
    public $group = '__';

    /**
     * The order of the field i relation to the fields in a form
     * The default value is arbitratrily large to force last
     * @var float
     */
    public $order=100;

    /**
     * The url to edit page of item. If this is specified for an item, the edit link for the corresponding form is ignored for that item.
     *
     * @var string
     */
    public $editLink='';

    /**
     * Description text for the index page.
     *
     * @var string
     */
    public $indexDescription='';

    /**
     * Description text for the edit page.
     *
     * @var string
     */
    public $editDescription='';


    /**
     * When true, it will be assumed that the field has safe html which can be safely displayed on the index mode. Applicable for textarea. It is currently not used on the edit mode.
     *
     * @var boolean
     */
    public $indexAllowHTML=false;

    /**
     * If true fields like textarea will be displayed with html editor in the edit mode.
     *
     * @var boolean
     */
    public $isRichText=false;


    /**
     * Construct a new item from array; the item returned can be a field or fieldset or group based on the type array index
     *
     * @param array $props Must contain 'name' index which is actually not requird for a fieldset but this method still makes it a requirement for no reason.
     * @return FormItem The created item oy false;
     */
    public static function make($props)
    {

        //Lets first get the name
        $name = null;
        foreach ($props as $prop => $v) {
            if (str_is($prop, 'name')) {
                $name = $v;
                break;
            }
        }

        if (!$name) return false; // We cannot do without a name

        $item = null;
        if(isset($props['type'])){
            switch ($props['type']) {
                case FormItem::FIELDSET:
                    $item = new Fieldset($name);
                    break;
                case FormItem::GROUP:
                    $item = new Group($name);
                    break;
                default:
                    $item = new Field($name);
            }
        }else{
            $item = new Field($name);
        }


        foreach ($props as $prop => $v) {
            if (str_is($prop, 'name')) {
                continue;
            }
            $item->$prop = $v;
        }

        return $item;
    }
}

