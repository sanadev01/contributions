<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ColombiaShippingFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'colombia_service';
    }
}
