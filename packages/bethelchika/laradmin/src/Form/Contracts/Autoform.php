<?php
namespace BethelChika\Laradmin\Form\Contracts;
use BethelChika\Laradmin\User;
use BethelChika\Laradmin\Form\Form;
use BethelChika\Laradmin\Form\Contracts\Fieldable;

abstract class Autoform  extends Form implements Fieldable{

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

   


}