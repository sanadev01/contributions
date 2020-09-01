<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use LaravelJsonColumn\Traits\JsonColumn;
use Illuminate\Database\Eloquent\Builder;

class HandlingService extends Model
{
    use JsonColumn;

    protected $guarded = [];

    const TYPE_HANDELING = 'handling';
    const TYPE_QUESTION = 'question';

    protected $casts = [
        'extra_data' => 'Object'
    ];

    public function scopeHandling(Builder $query)
    {
        return $query->where('type', self::TYPE_HANDELING);
    }

    public function scopeQuestion(Builder $query)
    {
        return $query->where('type', self::TYPE_QUESTION);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public static function getListOfTypes()
    {
        return [
            self::TYPE_HANDELING => self::normalizeString(self::TYPE_HANDELING),
            self::TYPE_QUESTION => self::normalizeString(self::TYPE_QUESTION)
        ];
    }

    public static function normalizeString($string)
    {
        return Str::of($string)->replace('_', ' ')->title();
    }

    public function getType()
    {
        return self::normalizeString($this->type);
    }

    public function isRateInPercent()
    {
        return $this->rate_type != 'flat';
    }

    public function isQtyRequired()
    {
        return $this->required_qty;
    }
}
