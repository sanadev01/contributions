<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelJsonColumn\Traits\JsonColumn;
use Spatie\Activitylog\Traits\LogsActivity;

class PoBox extends Model
{
    use JsonColumn;

    protected $casts = [
        'extra_data' => 'Array'
    ];

    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function getCompleteAddress()
    {
        $address = '';
        $address .= $this->address;
        $address .= '<br>';
        $address .= " {$this->city}, ";
        $address .= optional($this->state)->code;
        $address .= ", {$this->zipcode}";
        $address .= '<br>';
        $address .=  optional($this->country)->name;
        $address .= '<br>';
        $address .= 'Ph#: '.$this->phone;
        return $address;

    }
}
