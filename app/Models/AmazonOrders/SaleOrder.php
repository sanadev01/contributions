<?php

namespace App\Models\AmazonOrders;

use App\Models\Marketplace;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class SaleOrder
 * @package App\Models\AmazonOrders
 * @property integer id
 * @property integer user_id
 * @property integer marketplace_id
 * @property string amazon_order_id
 * @property string seller_order_id
 * @property Carbon purchase_date
 * @property Carbon last_update_date
 * @property string order_status
 * @property string fulfillment_channel
 * @property string sales_channel
 * @property string order_channel
 * @property string ship_service_level
 * @property double order_total
 * @property integer number_of_items_shipped
 * @property integer number_of_items_unshipped
 * @property string payment_execution_detail
 * @property string payment_method
 * @property string payment_method_details
 * @property string shipment_service_level_category
 * @property string easy_ship_shipment_status
 * @property string cba_displayable_shipping_label
 * @property string order_type
 * @property Carbon earliest_ship_date
 * @property Carbon latest_ship_date
 * @property Carbon earliest_delivery_date
 * @property Carbon latest_delivery_date
 * @property boolean is_business_order
 * @property boolean is_prime
 * @property boolean is_global_express_enabled
 * @property boolean is_premium_order
 * @property string replaced_order_id
 * @property boolean is_replacement_order
 * @property Carbon promise_response_due_date
 * @property boolean is_estimated_ship_date_set
 * @property boolean is_sold_by_ab
 * @property string fulfillment_instruction
 * @property boolean is_ispu
 * @property User user
 * @property Marketplace marketplace
 * @property SaleOrderItem sale_order_items
 * @property BuyerInfo buyer_info
 * @property ShipFromAddress ship_from_address
 * @property ShipToAddress ship_to_address
 */
class SaleOrder extends Model {

    protected $fillable = [
        'user_id',
        'marketplace_id',
        'amazon_order_id',
        'seller_order_id',
        'purchase_date',
        'last_update_date',
        'order_status',
        'fulfillment_channel',
        'sales_channel',
        'order_channel',
        'ship_service_level',
        'order_total',
        'number_of_items_shipped',
        'number_of_items_unshipped',
        'payment_execution_detail',
        'payment_method',
        'payment_method_details',
        'shipment_service_level_category',
        'easy_ship_shipment_status',
        'cba_displayable_shipping_label',
        'order_type',
        'earliest_ship_date',
        'latest_ship_date',
        'earliest_delivery_date',
        'latest_delivery_date',
        'is_business_order',
        'is_prime',
        'is_premium_order',
        'is_global_express_enabled',
        'replaced_order_id',
        'is_replacement_order',
        'promise_response_due_date',
        'is_estimated_ship_date_set',
        'is_sold_by_ab',
        'fulfillment_instruction',
        'is_ispu',
    ];

    protected $casts = [
        'last_update_date'       => 'datetime',
        'earliest_delivery_date' => 'datetime',
        'earliest_ship_date'     => 'datetime',
        'latest_delivery_date'   => 'datetime',
        'latest_ship_date'       => 'datetime',
        'purchase_date'          => 'datetime',
        'updated_at'             => 'datetime',
        'created_at'             => 'datetime'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function marketplace(): BelongsTo {
        return $this->belongsTo(Marketplace::class);
    }

    /**
     * @return HasMany
     */
    public function sale_order_items(): HasMany {
        return $this->hasMany(SaleOrderItem::class);
    }

    /**
     * @return HasOne
     */
    public function buyer_info(): HasOne {
        return $this->hasOne(BuyerInfo::Class);
    }

    /**
     * @return HasOne
     */
    public function ship_from_address(): HasOne {
        return $this->hasOne(ShipFromAddress::Class);
    }

    /**
     * @return HasOne
     */
    public function ship_to_address(): HasOne {
        return $this->hasOne(ShipToAddress::Class);
    }
}
