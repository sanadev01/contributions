<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelJsonColumn\Traits\JsonColumn;

class ProfitPackage extends Model
{
    use JsonColumn;
    protected $guarded = [];

    const SERVICE_BPS = 'bps';
    const SERVICE_PACKAGE_PLUS = 'package-plus';

    const TYPE_DEFAULT = 'default';
    const TYPE_PACKAGE = 'package';

}
