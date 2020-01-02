<?php
namespace BethelChika\Laradmin\Media\Exceptions;

class ReservedImageSizeException extends \Exception {
  protected $message = 'The image size is reserved';  
  
}