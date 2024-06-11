<?php

namespace App\Models;

use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use App\Services\Converters\UnitsConverter;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    
    const WEIGHT_PERCENTAGE = 0.1;

    protected $fillable = [
        'user_id',
        'name',
        'price',
        'sku',
        'status',
        'order',
        'category',
        'brand',
        'manufacturer',
        'barcode',
        'description',
        'quantity',
        'item', 
        'lot',
        'unit', 
        'case', 
        'inventory_value',
        'min_quantity',
        'max_quantity',
        'discontinued',
        'store_day',
        'location',
        'sh_code',
        'weight',
        'measurement_unit',
        'exp_date'
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function getStatusClass()
    {
        $class = "";
        if ( $this->status == 'pending' ){
            $class = 'btn btn-sm btn-danger';
        }

        if ( $this->status == 'approved' ){
            $class = 'btn btn-sm btn-success';
        }
        return $class;
    }

    public function fileInvoice()
    {
        return $this->belongsTo(Document::class,'invoice_file');
    }

   
    public function attachInvoice(UploadedFile $file)
    {
        optional($this->fileInvoice)->delete();
        $invoiceFile = Document::saveDocument(
            $file
        );
        
        $invoice = Document::create([
            'name' => $invoiceFile->getClientOriginalName(),
            'size' => $invoiceFile->getSize(),
            'type' => $invoiceFile->getMimeType(),
            'path' => $invoiceFile->filename
        ]);

        return $this->update([
            'invoice_file' => $invoice->id
        ]);
    }
    
    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public function setSkuAttribute($value)
    {
        $this->attributes['sku'] = strtoupper($value);
    }

    public function getWeightInKg()
    {
        return ($this->measurement_unit == 'kg/cm') ? round($this->weight, 2) : UnitsConverter::poundToKg($this->weight);
    }
}
