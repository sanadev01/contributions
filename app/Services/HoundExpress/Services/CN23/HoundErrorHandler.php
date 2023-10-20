<?php
namespace App\Services\HoundExpress\Services\CN23;

use App\Models\ShippingService;
use App\Services\Converters\UnitsConverter;
use App\Services\HoundExpress\Services\CN23\HoundReceiver;
use App\Services\HoundExpress\Services\CN23\HoundSender;
use App\Services\HoundExpress\Services\CN23\HoundPackagepiece;
class HoundErrorHandler { 
    private $error;
    public function __construct($response_body){
        $this->error='';
        if(count($response_body->wsErrors)){
            foreach($response_body->wsErrors as $key=>$e){ 
                $this->error.=$e->code." : ";
                $this->error.=$e->description. (count($response_body->wsErrors) ==$key+1? ' !' : ' !<br>');
            }           
        }
        else{
            $this->error=null;
        }

    }
    function getError(){
        return $this->error;
    }
}