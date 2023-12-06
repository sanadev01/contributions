<?php

namespace App\Models\AmazonOrders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ShipFromAddress
 * @package App\Models\AmazonOrders
 * @property integer id
 * @property integer user_id
 * @property integer sale_order_id
 * @property string name
 * @property string address_line1
 * @property string address_line2
 * @property string address_line3
 * @property string city
 * @property string country
 * @property string district
 * @property string state_or_region
 * @property string municipality
 * @property string postal_code
 * @property string country_code
 * @property string phone
 * @property string address_type
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property User user
 * @property SaleOrder sale_order
 */
class ShipFromAddress extends Model {

    protected $fillable = [
        'user_id',
        'sale_order_id',
        'name',
        'address_line1',
        'address_line2',
        'address_line3',
        'city',
        'country',
        'district',
        'state_or_region',
        'municipality',
        'postal_code',
        'country_code',
        'phone',
        'address_type'
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
