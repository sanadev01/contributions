<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelJsonColumn\Traits\JsonColumn;

class Connect extends Model
{
    use JsonColumn;

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
