<?php

namespace App\Models;

use App\Models\OrderTracking;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Models\Warehouse\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Services\Converters\UnitsConverter;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Services\Correios\Contracts\Package;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\Calculators\WeightCalculator;
use App\Services\Correios\Models\Package as ModelsPackage;

class Order extends Model implements Package
{

    use SoftDeletes;
    protected $guarded = [];

    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected $casts = [
       'cn23' => 'Array',
       'order_date' => 'datetime'
    ];

    const STATUS_PREALERT_TRANSIT = 10;
    const STATUS_PREALERT_READY = 20;
    const STATUS_CONSOLIDATOIN_REQUEST = 25;
    const STATUS_CONSOLIDATED = 26;

    const STATUS_ORDER = 30;
    const STATUS_NEEDS_PROCESSING = 32;

    const STATUS_CANCEL = 35;
    const STATUS_REJECTED = 38;
    const STATUS_RELEASE = 40;

    const STATUS_REFUND = 50;

    const STATUS_PAYMENT_PENDING = 60;
    const STATUS_PAYMENT_DONE = 70;
    const STATUS_ARRIVE_AT_WAREHOUSE = 73;
    const STATUS_INSIDE_CONTAINER = 75;
    const STATUS_SHIPPED = 80;
    const STATUS_BRAZIL_POSTED = 01;

    const BRAZIL = 30;
    const CHILE = 46;
    const USPS = 250;

    public $user_profit = 0;

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

    public function getSenderFullName()
    {
        return $this->sender_first_name.' '.$this->sender_last_name;
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

    public function containers()
    {
        return $this->belongsToMany(Container::class);
    }
    
    public function deposits()
    {
        return $this->belongsToMany(Deposit::class);
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

    public function isNeedsProcessing()
    {
        return $this->status == self::STATUS_NEEDS_PROCESSING;
    }
    
    public function isShipped()
    {
        return $this->status == self::STATUS_SHIPPED;
    }
    
    public function isRefund()
    {
        return $this->status == self::STATUS_REFUND;
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
            return round($weight,2);
        }

        if ( $unit == 'lbs' && !$this->isWeightInKg() ){
            return round($weight);
        }

        if ( $unit == 'lbs' && $this->isWeightInKg() ){
            return round(UnitsConverter::kgToPound($weight),2);
        }

        if ( $unit == 'kg' && !$this->isWeightInKg() ){
            return round(UnitsConverter::poundToKg($weight),2);
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

    public function hasBattery()
    {
        return $this->items()->where('contains_battery',true)->count() > 0;
    }

    public function hasPerfume()
    {
        return $this->items()->where('contains_perfume',true)->count() > 0;
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

        if($this->recipient->country_id == 250)
        {
            $shippingCost = $this->user_declared_freight;
            $this->calculateProfit($shippingCost);

        } else {
            $shippingCost = $shippingService->getRateFor($this,true,$onVolumetricWeight);
        }

        
        $additionalServicesCost = $this->services()->sum('price');

        $battriesExtra = $shippingService->contains_battery_charges * ( $this->items()->batteries()->count() );
        $pefumeExtra = $shippingService->contains_perfume_charges * ( $this->items()->perfumes()->count() );

        $dangrousGoodsCost = (isset($this->user->perfume) && $this->user->perfume == 1 ? 0 : $pefumeExtra) + (isset($this->user->battery) && $this->user->battery == 1 ? 0 : $battriesExtra);

        $consolidation = $this->isConsolidated() ?  setting('CONSOLIDATION_CHARGES',0,null,true) : 0;



        $total = $shippingCost + $additionalServicesCost + $this->insurance_value + $dangrousGoodsCost + $consolidation + $this->user_profit;

        $discount = 0; // not implemented yet
        $gross_total = $total - $discount;


        $this->update([
            'consolidation' => $consolidation,
            'order_value' => $this->items()->sum(\DB::raw('quantity * value')),
            'shipping_value' => $shippingCost,
            'dangrous_goods' => $dangrousGoodsCost,
            'total' => $total,
            'discount' => $discount,
            'gross_total' => $gross_total,
            'user_declared_freight' => $this->user_declared_freight
            // 'user_declared_freight' => $this->user_declared_freight >0 ? $this->user_declared_freight : $shippingCost
        ]);

    }

    public function calculateProfit($shippingCost)
    {
        $profit = $this->user->api_profit / 100;
        
        $this->user_profit = $shippingCost * $profit;

        return true;
    }

    public function addAffiliateCommissionSale(User $referrer, $commissionCalculator)
    {
        return $this->affiliateSale()->create( [
            'value' => $commissionCalculator->getValue(),
            'type' => $commissionCalculator->getCommissionSetting()->type,
            'commission' => $commissionCalculator->getCommission(),
            'user_id' => $referrer->id,
            'detail' => 'Commission from order '. $this->warehouse_number,
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

        if ( $this->status == Order::STATUS_NEEDS_PROCESSING ){
            $class = 'btn btn-sm btn-warning text-dark';
        }
        
        if ( $this->status == Order::STATUS_CANCEL ){
            $class = 'btn btn-sm btn-cancelled bg-cancelled';
        }
        if ( $this->status == Order::STATUS_REJECTED ){
            $class = 'btn btn-sm btn-cancelled bg-cancelled';
        }
        if ( $this->status == Order::STATUS_RELEASE ){
            $class = 'btn btn-sm btn-warning';
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
        
        if ( $this->status == Order::STATUS_REFUND ){
            $class = 'btn btn-sm btn-refund text-white';
        }

        return $class;
    }


    public function getDistributionModality(): int
    {
        return __default( optional($this->shippingService)->service_sub_class ,ModelsPackage::SERVICE_CLASS_STANDARD );
    }

    public function getService(): int
    {
        return 2;
    }

    public function getOrderValue()
    {
        return $this->items()->sum(DB::raw('quantity * value'));
    }

    public function sender_country()
    {
        return $this->belongsTo(Country::class, 'sender_country_id');
    }

    public function trackings()
    {
        return $this->hasMany(OrderTracking::class, 'order_id');
    }
}
