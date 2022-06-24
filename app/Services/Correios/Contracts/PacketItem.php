<?php


namespace App\Services\Correios\Contracts;


class PacketItem
{
    public $hsCode = null;
    public $description = null;
    public $quantity = 1;
    public $value = 0;


    public function toString()
    {
       return json_encode($this);
    }
}
