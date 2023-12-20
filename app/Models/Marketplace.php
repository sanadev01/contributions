<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Marketplace
 * @package App\Models
 * @property integer id
 * @property string name
 * @property string code
 * @property string marketplace_id
 * @property string mws_domain
 * @property string amazon_url
 * @property string currency
 * @property string timezone
 * @property string region_code
 * @property string region_name
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Marketplace extends Model {

    const REGION_NA = 'na';
    const REGION_EU = 'eu';
    const REGION_FE = 'fe';

    protected $fillable = [
        'name',
        'code',
        'marketplace_id',
        'amazon_url',
        'mws_domain',
        'currency',
        'timezone',
        'region_code',
        'region_name'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function getIcon(): string {
        return strtolower($this->code === 'UK' ? 'GB' : $this->code);
    }

    public static function getByRegion($region_code) {
        return self::query()
            ->where('region_code', $region_code)
            ->orderBy('id')
            ->get();
    }

    public static function getById($marketplace_id) {
        return self::query()->where('marketplace_id', $marketplace_id)->first();
    }
}
