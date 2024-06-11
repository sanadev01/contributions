<?php

namespace App\Models\AmazonOrders;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SaleOrderItem
 * @package App\Models\AmazonOrders
 * @property integer id
 * @property integer sale_order_id
 * @property string order_item_id
 * @property integer product_id
 * @property integer quantity_ordered
 * @property integer quantity_shipped
 * @property integer number_of_items
 * @property double item_price
 * @property double item_tax
 * @property double shipping_price
 * @property double shipping_tax
 * @property double gift_wrap_price
 * @property double gift_wrap_tax
 * @property string gift_message_text
 * @property string gift_wrap_level
 * @property string buyer_customized_info
 * @property double shipping_discount
 * @property double shipping_discount_tax
 * @property double promotion_discount
 * @property double promotion_discount_tax
 * @property array promotion_ids
 * @property double cod_fee
 * @property double cod_fee_discount
 * @property boolean is_gift
 * @property string condition_note
 * @property string condition_id
 * @property string condition_sub_type_id
 * @property Carbon scheduled_delivery_start_date
 * @property Carbon scheduled_delivery_end_date
 * @property string price_designation
 * @property boolean serial_number_required
 * @property boolean is_transparency
 * @property string ioss_number
 * @property string deemed_reseller_category
 * @property string granted_points
 * @property string tax_collection
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property SaleOrder sale_order
 * @property Product product
 */
class SaleOrderItem extends Model {

    protected $fillable = [
        'sale_order_id',
        'order_item_id',
        'product_id',
        'quantity_ordered',
        'quantity_shipped',
        'number_of_items',
        'item_price',
        'item_tax',
        'shipping_price',
        'shipping_tax',
        'gift_wrap_price',
        'gift_wrap_tax',
        'gift_message_text',
        'gift_wrap_level',
        'buyer_customized_info',
        'shipping_discount',
        'shipping_discount_tax',
        'promotion_discount',
        'promotion_discount_tax',
        'promotion_ids',
        'cod_fee',
        'cod_fee_discount',
        'is_gift',
        'condition_note',
        'condition_id',
        'condition_sub_type_id',
        'scheduled_delivery_start_date',
        'scheduled_delivery_end_date',
        'price_designation',
        'serial_number_required',
        'is_transparency',
        'ioss_number',
        'deemed_reseller_category',
        'granted_points',
        'tax_collection',
    ];

    protected $casts = [
        'scheduled_delivery_start_date' => 'datetime',
        'scheduled_delivery_end_date'   => 'datetime',
        'created_at'                    => 'datetime',
        'updated_at'                    => 'datetime'
    ];

    /**
     * @return BelongsTo
     */
    public function sale_order(): BelongsTo {
        return $this->belongsTo(SaleOrder::class);
    }

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo {
        return $this->belongsTo(Product::class);
    }

}
