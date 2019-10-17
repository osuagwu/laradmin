<?php
namespace BethelChika\Laradmin\Theme;

use BethelChika\Laradmin\Theme\Contracts\Theme;

class DefaultTheme extends Theme{


    public function __construct(){
        $this->name='Default theme';
        $this->from=self::$defaultFrom;   
    }
}