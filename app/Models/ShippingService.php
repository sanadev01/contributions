<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelJsonColumn\Traits\JsonColumn;
use Illuminate\Database\Eloquent\Builder;

class ShippingService extends Model
{
    use JsonColumn;

    protected $guarded = [];
}
