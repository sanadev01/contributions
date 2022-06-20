<?php

namespace App\Services\Correios\Models;

use Illuminate\Contracts\Support\Jsonable;

class BaseModel implements Jsonable{

    public function toJson($options = 0)
    {
        return json_encode($this);
    }
}