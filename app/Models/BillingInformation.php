<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class BillingInformation extends Model
{
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'card_no',
        'expiration',
        'cvv',
        'phone',
        'address',
        'state',
        'zipcode',
        'country'
    ];
    public function setCardNoAttribute($value)
    {
        $this->attributes['card_no'] = encrypt($value);
    }

    // Set up mutator for cvv
    public function setCvvAttribute($value)
    {
        $this->attributes['cvv'] = encrypt($value);
    }
    public function getCardNoAttribute($value)
    {
        if (is_null($value) || $value === '') {
            return $value;
        }
        try {
            return decrypt($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            \Log::error('Decryption error: ' . $e->getMessage());
            return $value;
        }
    }
    public function getCvvAttribute($value)
    {
        if (is_null($value) || $value === '') {
            return $value;
        }
        try {
            return decrypt($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            \Log::error('Decryption error: ' . $e->getMessage());
            return $value;
        }
    }
}
