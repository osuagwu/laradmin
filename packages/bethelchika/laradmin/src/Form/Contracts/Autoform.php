<?php
namespace BethelChika\Laradmin\Form\Contracts;
use BethelChika\Laradmin\User;
use BethelChika\Laradmin\Form\Form;
use BethelChika\Laradmin\Form\Contracts\Fieldable;

abstract class Autoform  extends Form implements Fieldable{
    
    /**
     * The route to go when form is closed
     *
     * @var string
     */
    public $navCloseRoute='user-profile';

    /**
     * The route param for the closeing of form
     *
     * @var array
     */
    public $navCloseRouteParam=[];

     /**
     * The route to go when form is closed
     *
     * @var string
     */
    public $navCloseUrl=null;

    /**
     * Gate for autorisation for a giving user
     *
     * @param User $user
     * @return boolean
     */
    abstract function gate(User $user);

    /**
     * Provide a chance make settings for the form
     *
     * @return void
     */
    abstract function build();

    /**
     * Returns the route to go when form is closed
     *
     * @return string
     */
    public function getNavCloseLink(){
        return $this->navCloseUrl??route($this->navCloseRoute,$this->navCloseRouteParam);
    }

    /**
     * Gets the link for form
     *
     * @return string
     */
    public function getLink(){
        return $this->manager()->autoformLink($this->getPack(),$this->getTag());
    }
    

    /**
     * Gets the link for editing form
     *
     * @return string
     */
    public function getEditLink(){
        return $this->manager()->autoformEditLink($this->getPack(),$this->getTag());
    }

    
    
   


}