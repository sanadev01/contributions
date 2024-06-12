<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelJsonColumn\Traits\JsonColumn;
use Spatie\Activitylog\Traits\LogsActivity;

class Connect extends Model
{
    use JsonColumn;

    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    
    protected $casts = [
        'extra_data' => 'Array'
    ];

    const TYPE_SHOPIFY = 'shopify';
    
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
