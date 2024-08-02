<?php


namespace App\Services\Correios\Models;


class PackageError
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getErrors()
    {
        if (json_decode($this->data)){
            
            $this->data = json_decode($this->data);
            \Log::info(['response data'=>$this->data]);
            $errorMessage = optional(optional($this->data)->msgs)[0] . optional($this->data)->msg;
            return $this->data ?  $errorMessage: optional($this)->data;
        }

        return $this->data;
    }

    public function __toString()
    {
        return $this->getErrors();
    }
}
