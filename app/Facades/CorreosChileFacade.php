<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class CorreosChileFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'CorreosChile_service';
    }
}
