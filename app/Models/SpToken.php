<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SpToken
 * @package App\Models
 * @property integer id
 * @property integer user_id
 * @property string refresh_token
 * @property string access_token
 * @property string token_type
 * @property Carbon expires_at
 * @property Carbon last_updated_at
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property User user
 */
class SpToken extends Model {

    protected $fillable = [
        'user_id',
        'refresh_token',
        'access_token',
        'token_type',
        'expires_at',
        'last_updated_at'
    ];

    protected $casts = [
        'expires_at'      => 'datetime',
        'last_updated_at' => 'datetime',
        'updated_at'      => 'datetime',
        'created_at'      => 'datetime'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

}
