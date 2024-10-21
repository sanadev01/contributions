<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ShCode extends Model
{
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    protected $fillable = [
        'code','description','type'
    ];
    function getIsFootWearAttribute() {
        $keyWords = ['shoes', 'shoe', 'sneaker', 'boots', 'sandals', 'slipper', 'footwear'];
        $isFootWear = false;
        foreach($keyWords as $word){
            if(strpos(strtolower($this->description), strtolower($word)) !== false){
                $isFootWear = true;
                break;
            }

           
        }
        return $isFootWear;
    }
}
