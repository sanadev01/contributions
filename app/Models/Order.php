<?php

namespace App\Models;

use App\Services\Calculators\CommissionCalculator;
use App\Services\Calculators\WeightCalculator;
use App\Services\Converters\UnitsConverter;
use Illuminate\Database\Eloquent\Builder;
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
    
    const STATUS_CONSOLIDATOIN_REQUEST = 25;
    const STATUS_CONSOLIDATED = 26;

    const STATUS_ORDER = 30;
    const STATUS_PAYMENT_PENDING = 60;
    const STATUS_PAYMENT_DONE = 70;
    const STATUS_SHIPPED = 80;
    
    public function scopeParcelReady(Builder $query)
    {
        return $query->where(function($query){
            $query->where('status', self::STATUS_PREALERT_READY)
                    ->orWhere('status', self::STATUS_CONSOLIDATED);
        });
    }

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

    public function parentOrder()
    {
        return $this->belongsToMany(Order::class,'order_orders','order_id','consolidated_with_id');
    }

    public function subOrders()
    {
        return $this->belongsToMany(Order::class,'order_orders','consolidated_with_id','order_id');
    }

    public function shippingService()
    {
        return $this->belongsTo(ShippingService::class,'shipping_service_id');
    }

    public function affiliateSale()
    {
        return $this->hasOne(AffiliateSale::class,'order_id');
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
        return !$this->paymentInvoices->isEmpty() ? $this->paymentInvoices->first() : null;
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
        if ( !$this->getPaymentInvoice() ){
            return $this->is_paid;
        }

        if ( !$this->getPaymentInvoice()->isPrePaid() ){
            return true;
        }

        return $this->getPaymentInvoice()->isPaid();
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

    public function syncServices(array $services)
    {
        $this->services()->delete();
        foreach($services as $serviceId){
            $service = HandlingService::find($serviceId);

            if (!$service ) continue;

            $this->services()->create([
                'service_id' => $service->id,
                'name' => $service->name,
                'cost' => $service->cost,
                'price' => $service->price,
            ]);
        }
    }

    public function isMeasurmentUnitCm()
    {
        return $this->measurement_unit == 'kg/cm';
    }

    public function isWeightInKg()
    {
        return $this->measurement_unit == 'kg/cm';
    }

    public function getOriginalWeight($unit='kg')
    {
        $weight = $this->weight;
        
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

    public function doCalculations($onVolumetricWeight=true)
    {
        $shippingService = $this->shippingService;

        $shippingCost = $shippingService->getRateFor($this,true,$onVolumetricWeight);
        $additionalServicesCost = $this->services()->sum('price');

        $battriesExtra = $shippingService->contains_battery_charges * ( $this->items()->batteries()->count() );
        $pefumeExtra = $shippingService->contains_perfume_charges * ( $this->items()->perfumes()->count() );

        $dangrousGoodsCost = $battriesExtra + $pefumeExtra;

        $consolidation = $this->isConsolidated() ?  setting('CONSOLIDATION_CHARGES',0,null,true) : 0;

        $commissionCalculator = new CommissionCalculator($this, $shippingCost);
        
        $commission = $commissionCalculator->getCommission();
        
        $total = $shippingCost + $additionalServicesCost + $commission + $this->insurance_value + $dangrousGoodsCost + $consolidation;
        
        $discount = 0; // not implemented yet
        $gross_total = $total - $discount;
        
        if($commissionCalculator->hasReferrer()){
           $referrer = $commissionCalculator->hasReferrer();
           //$referrer->commissionSetting
           $referrer->addAffiliateCommissionSale($this, $commissionCalculator );
        }
        $this->update([
            'consolidation' => $consolidation,
            'order_value' => $this->items()->sum(\DB::raw('quantity * value')),
            'shipping_value' => $shippingCost,
            'comission' => $commission,
            'dangrous_goods' => $dangrousGoodsCost,
            'total' => $total,
            'discount' => $discount,
            'gross_total' => $gross_total
        ]);

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

    public function getStatusClass()
    {
        $class = "";

        if ( $this->status == Order::STATUS_ORDER ){
            $class = 'btn btn-sm btn-info';
        }

        if ( $this->status == Order::STATUS_PAYMENT_PENDING ){
            $class = 'btn btn-sm btn-danger';
        }

        if ( $this->status == Order::STATUS_PAYMENT_DONE ){
            $class = 'btn btn-sm btn-success';
        }

        if ( $this->status == Order::STATUS_SHIPPED ){
            $class = 'btn btn-sm bg-secondary text-white';
        }

        return $class;
    }
}
