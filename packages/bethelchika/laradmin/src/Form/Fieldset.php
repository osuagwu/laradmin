<?php 
namespace BethelChika\Laradmin\Form;

use Illuminate\Support\Collection;

class Fieldset extends FormItem
{


    /**
     * Collection of fields
     *
     * @var Collection
     */
    private $fields;
    /**
 * The fieldset legend
 *
 * @var string
 */
    public $legend = null;
    /**
     * Construct a new fieldset
     *
     * @param string $name
     * @param string $type
     */
    public function __construct($name = null)
    {


        $this->type = FormItem::FIELDSET;
        $this->name = $name;
        $this->fields = new Collection;
    }

    /**
     * Add a field
     *
     * @param Field $field
     * @return void
     */
    public function addField(Field $field)
    {
        $this->fields->push($field);
    }

    /**
     * Add a collection fields
     *
     * @param Collection $fields
     * @return void
     */
    public function addFields(Collection $fields)
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }
    }


    /**
     * Returns the fields
     *
     * @return Collection
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @inheritDoc
     */
    public static function make($props)
    {
        $props['type'] = FormItem::FIELDSET;
        return parent::make($props);
    }
}

