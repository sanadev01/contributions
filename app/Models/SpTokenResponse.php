<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SpTokenResponse
 * @package App\Models
 * @property integer id
 * @property integer user_id
 * @property array header
 * @property array response
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property User user
 */
class SpTokenResponse extends Model {

    protected $fillable = [
        'user_id',
        'header',
        'response'
    ];

    protected $casts = [
        'header'     => 'array',
        'response'   => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

}
