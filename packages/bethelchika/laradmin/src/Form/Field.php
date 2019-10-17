<?php 
namespace BethelChika\Laradmin\Form;

class Field extends FormItem
{
    //TODO: Perhaps add a $js property like in menuitem| ALSO ADD $id property for html id parameter of th einput
    /**
     * Help message
     *
     * @var string
     */
    public $help;
    /**
     * Input placeholder text
     *
     * @var string
     */
    public $placeholder = null;


    /**
     * The unit of the field (e.g $,£,cm, etc.)
     *
     * @var string
     */
    public $unit = null;

    /**
     * CSS class for the box arround the field overall box
     *
     * @var string
     */
    public $class = null;

    /**
     * Inline style for the field html element
     *
     * @var string
     */
    public $style = null;

    /**
     * Current value of the field
     *
     * @var mixed
     */
    public $value;

    /**
     * All options for select field. This should assoc array where key=value in html select field. 
     *
     * @var array
     */
    public $options;
    /**
     * The Laravel validation rules that apply to this  field
     *  e.g: 'required|min:5'
     * @var string
     */
    public $rules;

    /**
     * Laravel validation messages
     * e.g: [
     *        'required'=>'Name must be given',
     *        'min'=>'Name cannot be more than five characters',
     *      ]
     * @var array
     */
    public $messages = [];

    /**
     * Read only items are only displayed for viewing.
     *
     * @var boolean
     */
    public $isReadOnly=false;

      /**
     * Write only items are only displayed for editing page.
     *
     * @var boolean
     */
    public $isWriteOnly=false;

    /**
     * Construct a new field
     *
     * @param string $name
     * @param string $type
     */
    public function __construct($name, $type = null)
    {
        $this->name = $name;
        $this->type = $type;
        if (!$type) {
            $this->type = FormItem::TEXT;
        }
    }

 
}

