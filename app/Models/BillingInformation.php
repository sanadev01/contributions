<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingInformation extends Model
{
    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'card_no', 'expiration', 'cvv', 'phone', 'address', 'state', 'zipcode', 'country'
    ];

}
