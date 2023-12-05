<?php

namespace App\Models\AmazonOrders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Buyer
 * @package App\Models\AmazonOrders
 * @property integer id
 * @property integer user_id
 * @property integer sale_order_id
 * @property string buyer_email
 * @property string buyer_name
 * @property string buyer_country
 * @property string buyer_tax_info
 * @property string purchase_order_number
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property User user
 * @property SaleOrder sale_order
 */
class BuyerInfo extends Model {

    protected $table = 'buyer_info';

    protected $fillable = [
        'user_id',
        'sale_order_id',
        'buyer_email',
        'buyer_name',
        'buyer_country',
        'buyer_tax_info',
        'purchase_order_number',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
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
    public function sale_order(): BelongsTo {
        return $this->belongsTo(SaleOrder::class);
    }

}
