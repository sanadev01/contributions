<?php

namespace App\Models;

use App\Services\Calculators\WeightCalculator;
use App\Services\Converters\UnitsConverter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;

class Order extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $casts = [
       'cn23' => 'Array',
       'order_date' => 'datetime'
    ];

    const STATUS_PREALERT_TRANSIT = 10;
    const STATUS_PREALERT_READY = 20;
    const STATUS_ORDER = 30;
    const STATUS_CONSOLIDATOIN_REQUEST = 40;
    const STATUS_CONSOLIDATED = 50;
    const STATUS_PAYMENT_PENDING = 60;
    const STATUS_PAYMENT_DONE = 70;
    const STATUS_SHIPPED = 80;
    

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentInvoices()
    {
        return $this->belongsToMany(PaymentInvoice::class);
    }

    public function recipient()
    {
        return $this->hasOne(Recipient::class,'order_id');
    }

    public function subOrders()
    {
        return $this->belongsToMany(Order::class,'order_orders','consolidated_with_id','order_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function services()
    {
        return $this->hasMany(OrderService::class);
    }

    public function getPaymentInvoice()
    {
        return !$this->paymentInvoices->isEmpty() ? $this->paymentInvoices()->first() : null;
    }

    public function isShipmentAdded()
    {
        return $this->is_shipment_added;
    }

    public function isConsolidated()
    {
        return $this->is_consolidated;
    }
    
    public function isPaid()
    {
        return  ( $this->is_paid  && !$this->getPaymentInvoice()) || ($this->getPaymentInvoice() && $this->getPaymentInvoice()->isPaid()) ;
    }

    public function isArrivedAtWarehouse()
    {
        return $this->is_received_from_sender;
    }

    public function purchaseInvoice()
    {
        return $this->belongsTo(Document::class,'purchase_invoice');
    }

    public function images()
    {
        return $this->belongsToMany(Document::class);
    }

    public function attachInvoice(UploadedFile $file)
    {
        optional($this->purchaseInvoice)->delete();
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
            'purchase_invoice' => $invoice->id
        ]);
    }

    public function isMeasurmentUnitCm()
    {
        return $this->measurement_unit == 'kg/cm';
    }

    public function isWeightInKg()
    {
        return $this->measurement_unit == 'kg/cm';
    }

    public function getWeight($unit='kg')
    {
        $orignalWeight =   $this->weight; //$this->isWeightInKg() ? $this->weight : UnitsConverter::poundToKg($this->weight);
        $volumnWeight = WeightCalculator::getVolumnWeight($this->length,$this->width,$this->height,$this->isWeightInKg()? 'cm' : 'in');

        $weight = $volumnWeight > $orignalWeight ? $volumnWeight : $orignalWeight;

        if ( $unit == 'kg' && $this->isWeightInKg() ){
            return $weight;
        }

        if ( $unit == 'lbs' && !$this->isWeightInKg() ){
            return $weight;
        }

        if ( $unit == 'lbs' && $this->isWeightInKg() ){
            return UnitsConverter::kgToPound($weight);
        }

        if ( $unit == 'kg' && !$this->isWeightInKg() ){
            return UnitsConverter::poundToKg($weight);
        }
    }

    public function setCN23(array $data)
    {
        $this->update([
            'cn23' => $data,
            'corrios_tracking_code' => $data['tracking_code']
        ]);
    }

    public function getCN23()
    {
        return $this->cn23 ? new TempModel($this->cn23): null;
    }

    public function hasCN23()
    {
        return $this->cn23 ? true: false;
    }

    public function getTempWhrNumber()
    {
        return "HD-{$this->id}";
    }


    /**
     * Accessors
     */
    public function getGrossTotalAttribute($value)
    {
        return round($value,2);
    }

    public function getTotalAttribute($value)
    {
        return round($value,2);
    }

    public function getShippingValueAttribute($value)
    {
        return round($value,2);
    }
}
