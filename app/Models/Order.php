<?php

namespace App\Models;

use App\Models\State;
use App\Models\ZoneCountry;
use App\Services\GSS\Client;
use App\Models\OrderTracking;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Models\Warehouse\Container;
use App\Models\Warehouse\AccrualRate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Services\Converters\UnitsConverter;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Services\Correios\Contracts\Package;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\Calculators\WeightCalculator;
use App\Services\Correios\Models\Package as ModelsPackage;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Spatie\Activitylog\LogOptions;

class Order extends Model implements Package
{

    use SoftDeletes;
    protected $guarded = [];

    use LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    protected $casts = [
        'cn23' => 'array',
        'order_date' => 'datetime',
        'us_secondary_label_cost' => 'array',
    ];

    const STATUS_INVENTORY_PENDING = 1;
    const STATUS_INVENTORY_IN_PROGRESS = 2;
    const STATUS_INVENTORY_CANCELLED = 3;
    const STATUS_INVENTORY_REJECTED = 4;
    const STATUS_INVENTORY_FULFILLED = 5;
    // const STATUS_INVENTORY = 5;
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
    const STATUS_DRIVER_RECIEVED = 72;
    const STATUS_ARRIVE_AT_WAREHOUSE = 73;
    const STATUS_INSIDE_CONTAINER = 75;
    const STATUS_SHIPPED = 80;
    const STATUS_BRAZIL_POSTED = 01;

    const BRAZIL = 30;
    const CHILE = 46;
    const Guatemala = 94;
    const US = 250;
    const PORTUGAL = 188;
    const COLOMBIA = 50;
    const Japan = 114;
    const UK = 249;

    public $user_profit = 0;
    public function scopeParcelReady(Builder $query)
    {
        return $query->where(function ($query) {
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
        return $this->sender_first_name . ' ' . $this->sender_last_name;
    }

    public function paymentInvoices()
    {
        return $this->belongsToMany(PaymentInvoice::class);
    }

    public function recipient()
    {
        return $this->hasOne(Recipient::class, 'order_id');
    }

    public function parentOrder()
    {
        return $this->belongsToMany(Order::class, 'order_orders', 'order_id', 'consolidated_with_id');
    }

    public function subOrders()
    {
        return $this->belongsToMany(Order::class, 'order_orders', 'consolidated_with_id', 'order_id');
    }

    public function shippingService()
    {
        return $this->belongsTo(ShippingService::class, 'shipping_service_id');
    }

    public function affiliateSale()
    {
        return $this->hasOne(AffiliateSale::class, 'order_id');
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
        if (!$this->getPaymentInvoice()) {
            return $this->is_paid;
        }

        if (!$this->getPaymentInvoice()->isPrePaid()) {
            return true;
        }

        return $this->getPaymentInvoice()->isPaid();
    }

    public function isNeedsProcessing()
    {
        return $this->status == self::STATUS_NEEDS_PROCESSING;
    }

    public function getIsShippedAttribute()
    {
        return $this->status == self::STATUS_SHIPPED;
    }

    public function getIsRefundAttribute()
    {
        return $this->status == self::STATUS_REFUND;
    }

    public function getIsArrivedAtWarehouseAttribute()
    {
        return $this->is_received_from_sender ?? false;
    }

    public function purchaseInvoice()
    {
        return $this->belongsTo(Document::class, 'purchase_invoice');
    }

    public function images()
    {
        return $this->belongsToMany(Document::class);
    }

    public function tax()
    {
        return $this->hasOne(Tax::class, 'order_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
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
        foreach ($services as $serviceId) {
            $service = HandlingService::find($serviceId);

            if (!$service) continue;

            $this->services()->create([
                'service_id' => $service->id,
                'name' => $service->name,
                'cost' => $service->cost,
                'price' => $service->price,
            ]);
        }
    }

    public function getIsWeightInKgAttribute()
    {
        return $this->measurement_unit == 'kg/cm';
    }

    public function getOriginalWeight($unit = 'kg')
    {
        $weight = $this->weight;

        if ($unit == 'kg' && $this->is_weight_in_kg) {
            return round($weight, 2);
        }

        if ($unit == 'lbs' && !$this->is_weight_in_kg) {
            return round($weight, 2);
        }

        if ($unit == 'lbs' && $this->is_weight_in_kg) {
            return round(UnitsConverter::kgToPound($weight), 2);
        }

        if ($unit == 'kg' && !$this->is_weight_in_kg) {
            return round(UnitsConverter::poundToKg($weight), 2);
        }
    }

    public function getWeight($unit = 'kg')
    {
        $orignalWeight =   $this->weight; //$this->is_weight_in_kg ? $this->weight : UnitsConverter::poundToKg($this->weight);
        $volumnWeight = WeightCalculator::getVolumnWeight($this->length, $this->width, $this->height, $this->is_weight_in_kg ? 'cm' : 'in');

        $weight = $volumnWeight > $orignalWeight ? $volumnWeight : $orignalWeight;

        if ($unit == 'kg' && $this->is_weight_in_kg) {
            return $weight;
        }

        if ($unit == 'lbs' && !$this->is_weight_in_kg) {
            return $weight;
        }

        if ($unit == 'lbs' && $this->is_weight_in_kg) {
            return UnitsConverter::kgToPound($weight);
        }

        if ($unit == 'kg' && !$this->is_weight_in_kg) {
            return UnitsConverter::poundToKg($weight);
        }
    }

    public function getHasBatteryAttribute()
    {
        return $this->items()->where('contains_battery', true)->count() > 0;
    }

    public function getHasPerfumeAttribute()
    {
        return $this->items()->where('contains_perfume', true)->count() > 0;
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
        return $this->cn23 ? new TempModel($this->cn23) : null;
    }

    public function hasCN23()
    {
        return $this->cn23 ? true : false;
    }

    public function getHasSecondLabelAttribute()
    {
        return $this->us_api_response ? true : false;
    }

    public function usLabelService()
    {
        return $this->has_second_label ? $this->us_api_service : null;
    }

    public function getCarrierAttribute()
    {
        return $this->carrierService();
    }

    public function carrierService()
    {
        return match ((int) optional($this->shippingService)->service_sub_class) {
            ShippingService::AJ_Standard_CN,
            ShippingService::AJ_Express_CN,
            ShippingService::BCN_Packet_Standard,
            ShippingService::BCN_Packet_Express,
            ShippingService::Packet_Express,
            ShippingService::Packet_Standard,
            ShippingService::Packet_Mini, => 'Correios Brazil',
            ShippingService::USPS_PRIORITY,
            ShippingService::USPS_FIRSTCLASS,
            ShippingService::USPS_PRIORITY_INTERNATIONAL,
            ShippingService::USPS_FIRSTCLASS_INTERNATIONAL,
            ShippingService::USPS_GROUND,
            ShippingService::GDE_PRIORITY_MAIL,
            ShippingService::GDE_FIRST_CLASS,
            ShippingService::GSS_PMI,
            ShippingService::GSS_EPMEI,
            ShippingService::GSS_EPMI,
            ShippingService::GSS_FCM,
            ShippingService::GSS_CEP,
            ShippingService::GSS_EMS => 'USPS',
            ShippingService::UPS_GROUND => 'UPS',
            ShippingService::FEDEX_GROUND => 'FEDEX',
            ShippingService::SRP,
            ShippingService::SRM => 'Correios Chile',
            ShippingService::GePS,
            ShippingService::GePS_EFormat,
            ShippingService::Parcel_Post => 'Global eParcel',
            ShippingService::Prime5,
            ShippingService::Prime5RIO,
            ShippingService::DirectLinkCanada,
            ShippingService::DirectLinkMexico,
            ShippingService::DirectLinkChile,
            ShippingService::DirectLinkAustralia => 'Prime5',
            ShippingService::Post_Plus_Registered,
            ShippingService::Post_Plus_EMS,
            ShippingService::Post_Plus_Prime,
            ShippingService::Post_Plus_Premium,
            ShippingService::LT_PRIME,
            ShippingService::Post_Plus_LT_Premium,
            ShippingService::Post_Plus_CO_EMS,
            ShippingService::Post_Plus_CO_REG => 'PostPlus',
            ShippingService::HoundExpress => 'Hound Express',
            ShippingService::TOTAL_EXPRESS => 'Total Express',
            ShippingService::HD_Express => 'HD Express',
            default => 'Correios Brazil',
        };
    }


    public function carrierCost()
    {
        return match ((int)optional($this->shippingService)->service_sub_class) {
            ShippingService::USPS_PRIORITY,
            ShippingService::USPS_FIRSTCLASS,
            ShippingService::USPS_PRIORITY_INTERNATIONAL,
            ShippingService::USPS_FIRSTCLASS_INTERNATIONAL,
            ShippingService::UPS_GROUND,
            ShippingService::FEDEX_GROUND,
            ShippingService::GePS,
            ShippingService::GePS_EFormat,
            ShippingService::Prime5,
            ShippingService::USPS_GROUND,
            ShippingService::Post_Plus_Registered,
            ShippingService::Post_Plus_EMS,
            ShippingService::Parcel_Post,
            ShippingService::Post_Plus_Prime,
            ShippingService::Post_Plus_Premium,
            ShippingService::Prime5RIO,
            ShippingService::HD_Express,
            ShippingService::GSS_PMI,
            ShippingService::GSS_EPMEI,
            ShippingService::GSS_EPMI,
            ShippingService::GSS_FCM,
            ShippingService::GSS_EMS => $this->user_declared_freight,
            default => $this->getValuePaidToCorreios(),
        };
    }
    private function getValuePaidToCorreios()
    {
        $rateSlab = AccrualRate::getCarrierRate($this->getWeight('kg'), optional($this->shippingService)->service_sub_class);
        if (!$rateSlab) {
            return 0;
        }
        $container = $this->containers->first();
        if (!$container) {
            return $rateSlab->gru;
        }
        switch ($container->destination_ariport) {
            case "GRU" || "Santiago":
                return $rateSlab->gru;
            default:
                return $rateSlab->cwb;
        }
    }

    /**
     * Sinerlog modification
     * This function sets sinerlog tran id
     */
    public function setSinerlogTrxId($trxId)
    {
        $this->update([
            'sinerlog_tran_id' => $trxId
        ]);
    }

    /**
     * Sinerlog modification
     * This function sets sinerlog freight price
     */
    public function setSinerlogFreight($freight)
    {
        $this->update([
            'sinerlog_freight' => $freight
        ]);
    }

    /**
     * Sinerlog modification
     * This function sets sinerlog url label
     */
    public function setSinerlogLabelURL($url)
    {
        $this->update([
            'sinerlog_url_label' => $url
        ]);
    }
    public function getTempWhrNumber($api = false)
    {
        $tempWhr = $this->change_id;
        $paddingLength = match (strlen($tempWhr)) {
            5 => '32023',
            6 => '2023',
            7 => '023',
            8 => '23',
            9 => '3',
            default => '',
        };
        $tempWhr = str_pad($tempWhr, 10, $paddingLength, STR_PAD_LEFT);
        return ($api ? 'TM' : 'HD') . "{$tempWhr}" . (optional($this->recipient)->country->code ?? "BR");
    }


    public function doCalculations($onVolumetricWeight = true, $isServices = false)
    {
        $shippingService = $this->shippingService;
        $additionalServicesCost = $this->calculateAdditionalServicesCost($this->services);
        if ($shippingService && in_array($shippingService->service_sub_class, [
            ShippingService::USPS_PRIORITY,
            ShippingService::USPS_FIRSTCLASS,
            ShippingService::USPS_PRIORITY_INTERNATIONAL,
            ShippingService::USPS_FIRSTCLASS_INTERNATIONAL,
            ShippingService::UPS_GROUND,
            ShippingService::FEDEX_GROUND,
            ShippingService::USPS_GROUND,
        ])) {
            $shippingCost = $this->user_declared_freight;
            $this->calculateProfit($shippingCost, $shippingService);
        }elseif ($shippingService && $shippingService->isGSSService()) {
            $this->calculateGSSProfit($shippingService);
            $shippingCost = $this->user_declared_freight;
        }else {
            $shippingCost = $shippingService->getRateFor($this,true,$onVolumetricWeight);
        }

        $battriesExtra = $shippingService->contains_battery_charges * ($this->items()->batteries()->count() ? 1 : 0);
        $pefumeExtra = $shippingService->contains_perfume_charges * ($this->items()->perfumes()->count() ? 1 : 0);

        // $dangrousGoodsCost = (isset($this->user->perfume) && $this->user->perfume == 1 ? 0 : $pefumeExtra) + (isset($this->user->battery) && $this->user->battery == 1 ? 0 : $battriesExtra);

        $dangrousGoodsCost = (setting('perfume', null, $this->user->id) ? 0 : $pefumeExtra) + (setting('battery', null, $this->user->id) ? 0 : $battriesExtra);
        $consolidation = $this->isConsolidated() ?  setting('CONSOLIDATION_CHARGES', 0, null, true) : 0;

        $total = $shippingCost + $additionalServicesCost + $this->insurance_value + $dangrousGoodsCost + $consolidation + $this->user_profit;

        $discount = 0; // not implemented yet
        $grossTotal = $total - $discount;

        $this->update([
            'consolidation' => $consolidation,
            'order_value' => $this->items()->sum(\DB::raw('quantity * value')),
            'shipping_value' => $shippingCost,
            'dangrous_goods' => $dangrousGoodsCost,
            'total' => $total,
            'discount' => $discount,
            'gross_total' => $grossTotal,
            'user_declared_freight' => $this->user_declared_freight
        ]);
        $taxAndDuty = (float)$this->calculate_tax_and_duty;
        $feeForTaxAndDuty = (float)$this->calculate_fee_for_tax_and_duty;
        $total = $grossTotal + $taxAndDuty + $feeForTaxAndDuty;        
        $grossTotal = $total - $discount;
        $this->update([
            'tax_and_duty' =>  $taxAndDuty,
            'fee_for_tax_and_duty' => $feeForTaxAndDuty,
            'total' => $total,
            'gross_total' => $grossTotal,
        ]);
    }

    public function calculateAdditionalServicesCost($services)
    {
        if ($this->user->insurance == false) {
            foreach ($services as $service) {
                if ($service->name == 'Insurance' || $service->name == 'Seguro') {
                    $this->updateInsuranceServicePrice($service);
                }
            }
        }
        return $services->sum('price');
    }
    private function updateInsuranceServicePrice($service)
    {
        $orderValue = $this->items()->sum(\DB::raw('quantity * value'));
        $totalInsurance = (3 / 100) * $orderValue;
        if ($totalInsurance > 35) {
            $service->price = $totalInsurance;
        }
    }

    public function calculateProfit($shippingCost, $shippingService)
    {
        $profit_percentage = match ((int)$shippingService->service_sub_class) {
            ShippingService::UPS_GROUND => setting('ups_profit', null, $this->user->id) ?? setting('ups_profit', null, User::ROLE_ADMIN),
            ShippingService::FEDEX_GROUND => setting('fedex_profit', null, $this->user->id) ?? setting('fedex_profit', null, User::ROLE_ADMIN),
            default => setting('usps_profit', null, $this->user->id) ?? setting('usps_profit', null, User::ROLE_ADMIN),
        };

        $profit = $profit_percentage / 100;

        $this->user_profit = $shippingCost * $profit;
        return true;
    }

    public function addAffiliateCommissionSale(User $referrer, $commissionCalculator)
    {
        return $this->affiliateSale()->create([
            'value' => $commissionCalculator->getValue(),
            'type' => $commissionCalculator->getCommissionSetting()->type,
            'commission' => $commissionCalculator->getCommission(),
            'user_id' => $referrer->id,
            'referrer_id' => $this->user_id,
            'detail' => 'Commission from order ' . $this->warehouse_number,
        ]);
    }
    /**
     * Accessors
     */
    public function getGrossTotalAttribute($value)
    {
        return round($value, 2);
    }

    public function getTotalAttribute($value)
    {
        return round($value, 2);
    }

    public function getShippingValueAttribute($value)
    {
        return round($value, 2);
    }

    public function getStatusClass()
    {
        return match ((int)$this->status) {
            Order::STATUS_INVENTORY_PENDING => 'btn btn-sm btn-info',
            Order::STATUS_INVENTORY_IN_PROGRESS => 'btn btn-sm btn-warning',
            Order::STATUS_INVENTORY_CANCELLED,
            Order::STATUS_INVENTORY_REJECTED,
            Order::STATUS_PREALERT_TRANSIT => 'btn btn-sm btn-danger',
            Order::STATUS_INVENTORY_FULFILLED => 'btn btn-sm btn-success',
            Order::STATUS_PREALERT_READY => 'btn btn-sm btn-primary',
            Order::STATUS_ORDER => 'btn btn-sm btn-info',
            Order::STATUS_NEEDS_PROCESSING => 'btn btn-sm btn-warning text-dark',
            Order::STATUS_CANCEL,
            Order::STATUS_REJECTED => 'btn btn-sm btn-cancelled bg-cancelled',
            Order::STATUS_RELEASE => 'btn btn-sm btn-warning',
            Order::STATUS_PAYMENT_PENDING => 'btn btn-sm btn-danger',
            Order::STATUS_PAYMENT_DONE => 'btn btn-sm btn-success',
            Order::STATUS_SHIPPED => 'btn btn-sm bg-secondary text-white',
            Order::STATUS_REFUND => 'btn btn-sm btn-refund text-white',
            default => '',
        };
    }



    public function getDistributionModality(): int
    {
        if ($this->shippingService && in_array($this->shippingService->service_sub_class, [
            ShippingService::AJ_Packet_Standard,
            ShippingService::AJ_Packet_Express,
        ])) {
            return __default($this->getCorrespondenceServiceCode($this->shippingService->service_sub_class), ModelsPackage::SERVICE_CLASS_STANDARD);
        }
        return __default(optional($this->shippingService)->service_sub_class, ModelsPackage::SERVICE_CLASS_STANDARD);
    }

    public function getService(): int
    {
        return 2;
    }

    public function getOrderItemsValueAttribute()
    {
        return $this->items()->sum(DB::raw('quantity * value'));
    }

    public function senderCountry()
    {
        return $this->belongsTo(Country::class, 'sender_country_id');
    }

    public function senderState()
    {
        return $this->belongsTo(State::class, 'sender_state_id');
    }

    public function trackings()
    {
        return $this->hasMany(OrderTracking::class, 'order_id');
    }

    public function driverTracking()
    {
        return $this->hasOne(OrderTracking::class, 'order_id')->where('status_code', self::STATUS_DRIVER_RECIEVED);
    }

    public function getUSLabelResponse()
    {
        return json_decode($this->us_api_response);
    }

    public function apiPickupResponse()
    {
        return $this->api_pickup_response ? json_decode($this->api_pickup_response) : null;
    }

    public function getIsInternationalAttribute()
    {
        return $this->recipient->country->id != Country::US;
    }

    public function discountCost($onVolumetricWeight = true)
    {
        $shippingService = $this->shippingService;

        if ($this->weight_discount && $shippingService && !in_array($shippingService->service_sub_class, [
            ShippingService::USPS_PRIORITY, ShippingService::USPS_FIRSTCLASS, ShippingService::USPS_PRIORITY_INTERNATIONAL,
            ShippingService::USPS_FIRSTCLASS_INTERNATIONAL, ShippingService::UPS_GROUND, ShippingService::FEDEX_GROUND, ShippingService::USPS_GROUND
        ])) {

            $additionalServicesCost = $this->calculateAdditionalServicesCost($this->services);

            $battriesExtra = $shippingService->contains_battery_charges * ($this->items()->batteries()->count());
            $pefumeExtra = $shippingService->contains_perfume_charges * ($this->items()->perfumes()->count());
            $dangrousGoodsCost = (isset($this->user->perfume) && $this->user->perfume == 1 ? 0 : $pefumeExtra) + (isset($this->user->battery) && $this->user->battery == 1 ? 0 : $battriesExtra);
            $consolidation = $this->isConsolidated() ?  setting('CONSOLIDATION_CHARGES', 0, null, true) : 0;

            $otherTotal = $additionalServicesCost + $this->insurance_value + $dangrousGoodsCost + $consolidation;

            $discountShippingRate = $shippingService->getRateFor($this, true, $onVolumetricWeight);
            $orignalShippingRate =  $shippingService->getOriginalRate($this);

            $originalTotal = $orignalShippingRate + $otherTotal;
            $discountedTotal = $discountShippingRate + $otherTotal;

            $discount = $originalTotal - $discountedTotal;

            return $discount;
        }

        return null;
    }



    public function getCorrespondenceServiceCode($serviceCode)
    {
        return in_array($serviceCode, ShippingService::EXPRESSES) ? ShippingService::Packet_Express : ShippingService::Packet_Standard;
    }

    public function getSecondCarrierServiceAttritube()
    {
        return match ((int)$this->us_api_service) {
            ShippingService::USPS_PRIORITY,
            ShippingService::USPS_FIRSTCLASS,
            ShippingService::USPS_PRIORITY_INTERNATIONAL,
            ShippingService::USPS_FIRSTCLASS_INTERNATIONAL,
            ShippingService::USPS_GROUND => 'USPS',
            ShippingService::UPS_GROUND => 'UPS',
            ShippingService::FEDEX_GROUND => 'FEDEX',
            default => null,
        };
    }

    public function getStatusNameAttribute()
    {
        return match ((int)$this->status) {
            Order::STATUS_PREALERT_TRANSIT => 'TRANSIT',
            Order::STATUS_PREALERT_READY => 'READY',
            Order::STATUS_REFUND => 'REFUND',
            Order::STATUS_ORDER => 'ORDER2',
            Order::STATUS_NEEDS_PROCESSING => 'PROCESSING',
            Order::STATUS_PAYMENT_PENDING => 'PAYMENT PENDING',
            Order::STATUS_PAYMENT_DONE => 'PAYMENT DONE',
            Order::STATUS_CANCEL => 'CANCEL',
            Order::STATUS_REJECTED => 'REJECTED',
            Order::STATUS_RELEASE => 'RELEASE',
            Order::STATUS_SHIPPED => 'SHIPPED',
            default => null,
        };
    }

    public function getChangeIdAttribute()
    {
        $id = $this->id;
        $date = explode(":", $this->created_at);
        $minute = $date[1];
        $sec = $date[2];
        $changed = '';
        switch (true) {
            case (strlen($id) <= 3): {
                    $changed = substr($id, 0, 3) . $minute . $sec;
                    break;
                }
            case (strlen($id) <= 6): {
                    $changed = substr($id, 0, 3) . $minute . substr($id, 3, 3) . $sec;
                    break;
                }
            case (strlen($id) <= 9): {
                    $changed = substr($id, 0, 3) . $minute . substr($id, 3, 3) . $sec . substr($id, 6, 3);
                    break;
                }
            case (strlen($id) >= 10): {
                    $changed = substr($id, 0, 3) . $minute . substr($id, 3, 6) . $sec . substr($id, 9);
                    break;
                }
        }
        return $changed;
    }
    public function resolveRouteBinding($encryptedId, $field = null)
    {
        try {
            return $this->withTrashed()->findOrFail(decrypt($encryptedId));
        } catch (Exception $e) {
            return $this->withTrashed()->findOrFail($encryptedId);
        }
    }

    public function getEncryptedIdAttribute()
    {
        return encrypt($this->id);
    }

    public function totalExpressLabelUrl()
    {
        if (!$this->api_response) {
            return null;
        }
        $decode = json_decode($this->api_response);
        return $decode->labelResponse->data->download_url;
    }
    function getCn23LabelUrlAttribute()
    {
        if ($this->shippingService->is_total_express) {
            return $this->totalExpressLabelUrl();
        }
        return null;
    }

    public function calculateGSSProfit($shippingService)
    {
        $gssProfit = ZoneCountry::where('shipping_service_id', $shippingService->id)
            ->where('country_id', $this->recipient->country_id)
            ->value('profit_percentage');

        $client = new Client();
        $response = $client->getCostRates($this, $shippingService);
        $data = optional($response)->getData();
        
        if($this->shippingService->service_sub_class == ShippingService::GSS_CEP && $data->isSuccess && $data->output > 0) {
            $this->update([
                'user_declared_freight' => $data->output,
            ]);
            $this->user_declared_freight = $data->output;

        } else {
            if($gssProfit) {

                if ($data->isSuccess && $data->output > 0){
                    $userGssProfit =  setting('gss_profit', null, $this->user_id);
                    $userProfit = ($userGssProfit >= 0 && $userGssProfit <= 100)?$userGssProfit:0;
                    $totalProfit =   $gssProfit + ( $gssProfit / 100 * $userProfit );
                    $profit = $data->output / 100 * ($totalProfit);
                    $price = round($data->output + $profit, 2);
                    // dd($price, $profit, $totalProfit, $this->shipping_value);
                    $this->update([
                        'user_declared_freight' => $price,
                    ]);
                    $this->user_declared_freight = $price;
                }
    
            }
        }
        return true;
    }
    public function updateShippingServiceFromSetting()
    {
        if ($this->shippingService->is_anjun_service || $this->shippingService->is_bcn_service || $this->shippingService->is_correios_service) {
            if ($this->corrios_tracking_code) {
                return $this;
            }
            $serviceSubClass = $this->shippingService->service_sub_class;
            $standard = in_array($serviceSubClass, ShippingService::STANDARDS);
            $serviceSubClassMap = [
                'china_anjun_api' => $standard ? ShippingService::AJ_Standard_CN : ShippingService::AJ_Express_CN,
                'correios_api' => $standard ? ShippingService::Packet_Standard : ShippingService::Packet_Express,
                'bcn_api' => $standard ? ShippingService::BCN_Packet_Standard : ShippingService::BCN_Packet_Express,
                'anjun_api' => $standard ? ShippingService::AJ_Packet_Standard : ShippingService::AJ_Packet_Express,
            ];
            foreach ($serviceSubClassMap as $settingName => $subClass) {
                if (setting($settingName, null, User::ROLE_ADMIN)) {
                    $serviceSubClass = $subClass;
                    break;
                }
            }
            $this->update([
                'shipping_service_id' => ShippingService::where('service_sub_class', $serviceSubClass)->first()->id,
            ]);
            return $this->refresh();
        }
        return $this;
    }
    // public function getCalculateTaxAndDutyAttribute(){
    //     $totalTaxAndDuty = 0;
    //     if (strtolower($this->tax_modality) == "ddp") {
    //         if ($this->recipient->country->code == "MX" || $this->recipient->country->code == "CA" || $this->recipient->country->code == "BR") {

    //             $totalCost = $this->gross_total + $this->insurance_value + $this->carrierCost();
    //             $duty = $totalCost > 50 ? $totalCost * .6 : 0;
    //             $totalCostOfTheProduct = $totalCost + $duty;
    //             $icms = .17;
    //             $totalIcms = $icms * $totalCostOfTheProduct;
    //             $totalTaxAndDuty = $duty + $totalIcms; 
    //             \Log::info([
    //                 'recipient country' => $this->recipient->country->code,
    //                 'gross total' => $this->gross_total,
    //                 'insurance value' => $this->insurance_value,
    //                 'carrierCost' => $this->carrierCost(),
    //                 'totalCost' => $totalCost,
    //                 'duty' => $duty,
    //                 'totalCostOfTheProduct' => $totalCostOfTheProduct,
    //                 'icms' => $icms,
    //                 'totalIcms' => $totalIcms,
    //                 'totalTaxAndDuty' => $totalTaxAndDuty, 
    //             ]);
    //         }
    //     }
    //     return round($totalTaxAndDuty, 2);
    // }
    public function getCalculateTaxAndDutyAttribute(){
        $totalTaxAndDuty = 0;
        if (strtolower($this->tax_modality) == "ddp" || setting('is_prc_user', null, $this->user_id)) {
            if ($this->recipient->country->code == "MX" || $this->recipient->country->code == "CA" || $this->recipient->country->code == "BR") {

                $totalCost = $this->shipping_value + $this->user_declared_freight + $this->insurance_value;
            
                $duty = $totalCost > 50 ? $totalCost * .60 :0; 
                $totalCostOfTheProduct = $totalCost + $duty;
                $icms = .17;
                $totalIcms = $icms * $totalCostOfTheProduct;
                $totalTaxAndDuty = $duty + $totalIcms;
                \Log::info([
                    'recipient country' => $this->recipient->country->code,
                    'user_declared_freight' => $this->user_declared_freight,
                    'insurance value' => $this->insurance_value,
                    'shipping_value' => $this->shipping_value,
                    'total' =>  $totalCost > 50 ? 'total is above 50' : 'total is under 50',
                    'totalCost' => $totalCost,
                    'duty' => $duty,
                    'totalCostOfTheProduct' => $totalCostOfTheProduct,
                    'icms' => $icms,
                    'totalIcms' => $totalIcms,
                    'totalTaxAndDuty' => $totalTaxAndDuty, 
                ]);
            }
        }
        return round($totalTaxAndDuty, 2);
    }
    public function getCalculateFeeForTaxAndDutyAttribute()
    {
        $fee=0;
        if($this->calculate_tax_and_duty){
            $flag=true;
                if(setting('prc_user_fee', null, $this->user_id)=="flat_fee"){
                    $fee = setting('prc_user_fee_flat', null, $this->user_id)??2;
                    \Log::info([
                        'fee type'=>'flat fee',
                        'fee'=>$fee,
                    ]);
                    $flag=false;
                }
                if(setting('prc_user_fee', null, $this->user_id)=="variable_fee"){
                    $percent = setting('prc_user_fee_variable', null, $this->user_id)??1;
                    $fee= $this->calculate_tax_and_duty/100 * $percent;
                    $fee= $fee <0.5? 0.5:$fee;
                    \Log::info([
                        'fee type'=>'variable fee',
                        'fee'=>$fee,
                    ]); 
                    $flag=false;
                }
                if($flag){
                $fee = $this->calculate_tax_and_duty*.01;
                $fee = $fee<0.5?0.5:$fee;
                }
        }
        return $fee;
    }

}
