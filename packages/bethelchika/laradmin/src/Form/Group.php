<?php 
namespace BethelChika\Laradmin\Form;



class Group extends FormItem
{



    /**
 * 
 *
 * @var string
 */
    public $indexDescription = null;
    /**
 * 
 *
 * @var string
 */
    public $editDescription = null;

    /**
 * 
 *
 * @var string
 */
    public $label = null;
    /**
     * Construct a new fieldset
     *
     * @param string $name
     * @param string $type
     */
    public function __construct($name = null)
    {


        $this->type = FormItem::GROUP;
        $this->name = $name;
        $this->group = parent::GROUP_GROUP_NAME;
    }

    /**
     * @inheritDoc
     */
    public static function make($props)
    {
        $props['type'] = FormItem::GROUP;
        return parent::make($props);
    }
}

