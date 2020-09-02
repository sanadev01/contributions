<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelJsonColumn\Traits\JsonColumn;

class Pobox extends Model
{
    use JsonColumn;

    protected $casts = [
        'extra_data' => 'Array'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function getCompleteAddress()
    {
        $address = '';

        $address .= $this->address;
        $address .= '<br>';
        $address .= " {$this->city}, {$this->state}, {$this->zipcode}";
        $address .= '<br>';
        $address .= Country::where('code',$this->country)->first()->name;
        $address .= 'test-zubair';
        $address .= '<br>';
        $address .= 'Ph#: '.$this->phone;
        return $address;

    }
}
