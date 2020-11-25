<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelJsonColumn\Traits\JsonColumn;

class ProfitPackage extends Model
{
    use JsonColumn;
    protected $guarded = [];

    protected $casts = [
        'data' => 'Array'
    ];

    const SERVICE_BPS = 'bps';
    const SERVICE_PACKAGE_PLUS = 'package-plus';
    const SERVICE_PACKAGE_PROFIT = 'package-profit';

    const TYPE_DEFAULT = 'default';
    const TYPE_PACKAGE = 'package';

}
