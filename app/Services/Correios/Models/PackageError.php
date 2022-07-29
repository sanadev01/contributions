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
            return $this->data ? optional($this->data->msgs)[0] : optional($this)->data;
        }

        return $this->data;
    }

    public function __toString()
    {
        return $this->getErrors();
    }
}
