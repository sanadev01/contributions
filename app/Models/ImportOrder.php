<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class ImportOrder extends Model
{
    use LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
                            ->logAll()
                            ->logOnlyDirty()
                            ->dontSubmitEmptyLogs();
    }
    protected $fillable = [
        'user_id', 
        'file_name', 
        'upload_path', 
        'total_orders', 
        'total_errors', 
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function importOrders()
    {
        return $this->hasMany(ImportedOrder::class, 'import_id');
    }
}
