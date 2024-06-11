<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;

class Setting extends Model
{
    const PACKAGE_PLUS_FREIGHT = 'package_plus_freight';
    const SHIPPING_SERVICE_PREFIX = 'shipping_service_status_';

    protected $guarded = [];

    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    
    /**
     * Save Setting.
     */
    public static function saveByKey($key, $value, $userId = 0)
    {
        $key = strtoupper($key);

        // Check if settings already exists with key
        if (self::getByKey($key, null, $userId) !== null) {
            // Update by key value
            $s = Setting::byKey($key, $userId)->update([
                'value' => $value
            ]);

            /**
             * Check if setting updated.
             */
            if ($s) {
                return self::getByKey($key, null, $userId);
            }

            return null;
        }

        /**
         * Create Setting By Key.
         */
        $s = Setting::create([
            'user_id' => $userId,
            'key' => $key,
            'value' => $value
        ]);

        if ($s) {
            return $s->value;
        }

        return false;
    }

    /**
     * Get Setting by Key.
     */
    public static function getByKey($key, $default = null, $userId)
    {
        $key = strtoupper($key);
        $setting = self::byKey($key, $userId)->first();

        // Check if setting exits against key
        if ($setting) {
            return $setting->value;
        }

        return $default;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scopes.
     */
    public function scopeByKey(Builder $query, $key, $userId) : Builder
    {
        return $query->where('key', $key)->where('user_id', $userId);
    }
}
