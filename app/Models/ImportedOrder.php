<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class ImportedOrder extends Model
{
    protected $fillable = [
        'user_id','import_id', 'shipping_service_id','shipping_service_name','merchant','carrier','tracking_id','customer_reference','weight',
        'length','width','height','measurement_unit','is_invoice_created','is_shipment_added','status','order_date','sender_first_name',
        'sender_last_name','sender_email','sender_phone','first_name','last_name','email','phone','address','address2','street_no',
        'zipcode','city','account_type','state_id','country_id','tax_id','user_declared_freight','quantity','value','description',
        'sh_code','contains_battery','contains_perfume','recipient','items','error','correios_tracking_code'
    ];

    protected $casts = ['items' => 'array','recipient' => 'array','error' => 'array'];

    use LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
                            ->logAll()
                            ->logOnlyDirty()
                            ->dontSubmitEmptyLogs();
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    
    public function importOrder()
    {
        return $this->belongsTo(ImportOrder::class,'import_id');
    }
}
