<?php
namespace BethelChika\Laradmin\Meta\Exceptions;

class MetaSaveContextException extends \Exception {
  protected $message = 'Meta save option must be performed in console context to avoid repetitive access to database at every page load.';  
  
}