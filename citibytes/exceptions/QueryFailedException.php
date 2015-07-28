<?php

namespace citibytes\exceptions;

//Exception indicating some problem in Database connection
class QueryFailedException extends \Exception
{

  // Redefine the exception so message isn't optional
  public function __construct($message, $code = 0, Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }

}

?>
