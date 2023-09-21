<?php 

namespace App\Services\Bps\Models;

class Parcel extends BaseModel{


    public $external_customer_id;

    public $external_reference_code;

    public $is_humanitarian = false;

    /**
     * @var App\Services\Bps\Models\ParcelDetails
     */
    public $parcel_details;

    /**
     * @var App\Services\Bps\Models\Sender
     */
    public $sender;
    
    /**
     * @var App\Services\Bps\Models\Recipient
     */
    public $recipient;

    /**
     * @var array  App\Services\Bps\Models\Item
     */
    public $items = [];
}