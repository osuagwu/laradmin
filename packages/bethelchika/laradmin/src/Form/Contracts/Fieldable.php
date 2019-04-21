<?php
namespace BethelChika\Laradmin\Form\Contracts;


use Illuminate\Support\Collection;
use BethelChika\Laradmin\Form\Field;



interface Fieldable{
    /**
     * Process/Stores the field. This method is called for each field in a form provided by this fieldable. The value of the fieldable is the supposedly new data.
     * @param $pack string The form pack
     * @param $tag string The tag for a form
     * @param Field $field
     * @return int Returns 0=>fail, 1=>success, -1=unchanged
     */
     public function handle($pack,$tag,Field $field);

      /**
     * Returns fields 
     * @param $pack string The form pack
     * @param string $tag Tag to a form 
     * @param string $mode Tells the the mode the fields will be displayed on: values are:{'index'=>readonly for index page,'edit'=>read/write for edit page}
     * @return Collection Fields
     * 
     */
    public function all($pack,$tag,$mode);
}