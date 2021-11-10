<?php

namespace App\Services\Sinerlog\Models;

class BaseModel
{
    public function __construct(...$argv)
    {
        if ( is_array($argv) ){
            foreach($argv as $key=>$value ){
                $this->{$key} = $value;
            }
        }
    }

    public function toArray()
    {
        $result = array();
        foreach ($this as $key => $value) {
            if (is_object($value)) {
                $result[$key] = $value->toArray();
            } elseif(!is_null($value)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public function __toString()
    {
        ini_set("precision", 14); ini_set("serialize_precision", -1);
        return json_encode($this->toArray());
    }

    public function toJson()
    {
        return $this->__toString();
    }
}
