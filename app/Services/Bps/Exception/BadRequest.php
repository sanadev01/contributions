<?php

namespace App\Services\Bps\Exception;

use Exception;
use Throwable;

class BadRequest extends Exception{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
