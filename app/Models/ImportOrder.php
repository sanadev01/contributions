<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ImportOrder extends Model
{
    protected $fillable = [
        'user_id', 
        'file_name', 
        'upload_path', 
        'total_orders', 
        'total_errors', 
    ];

    

    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function importOrders()
    {
        return $this->hasMany(ImportedOrder::class, 'import_id');
    }
}
