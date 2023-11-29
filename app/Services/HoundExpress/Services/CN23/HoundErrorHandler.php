<?php

namespace App\Services\HoundExpress\Services\CN23;

class HoundErrorHandler
{
    private $error;
    public function __construct($response_body)
    {
        $this->error = '';
        if (!isset($response_body->wsErrors)) {
            $this->error = null;
            return;
        }
        if (count($response_body->wsErrors)) {
            foreach ($response_body->wsErrors as $key => $e) {
                $this->error .= $e->code . " : ";
                $this->error .= $e->description . (count($response_body->wsErrors) == $key + 1 ? ' !' : ' !<br>');
            }
        } else {
            $this->error = null;
        }
    }
    function getError()
    {
        return $this->error;
    }
}
