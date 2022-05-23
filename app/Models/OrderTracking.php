<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class OrderTracking extends Model
{
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    
    protected $guarded = [];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = (Auth::check()) ? auth()->id() : null;
            $model->updated_by =  (Auth::check()) ? auth()->id() : null;
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function setCreatedAtAttribute()
    {
        $date = Carbon::now()->setTimezone('America/New_York');

        $this->attributes['created_at'] = $date;
    }

    public function setUpdatedAtAttribute()
    {
        $date = Carbon::now()->setTimezone('America/New_York');

        $this->attributes['updated_at'] = $date;
    }
}
