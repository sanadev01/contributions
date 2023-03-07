<?php

namespace App\Services\Excel\ImportCharges;

use App\Models\Rate;
use Illuminate\Http\UploadedFile;
use App\Services\Excel\AbstractImportService;
use App\Models\Country;
use App\Models\ShippingService;

class ImportRates extends AbstractImportService
{
    protected $shippingService;
    protected $countryId;

    public function __construct(UploadedFile $file, $shippingService, $countryId)
    {
        $this->shippingService = $shippingService;
        $this->countryId = $countryId;
        
        $filename = $this->importFile($file);
        
        parent::__construct(
            $this->getStoragePath($filename)
        );
    }
    
    public function handle()
    {
        return $this->readRatesFromFile();
    }
    
    public function readRatesFromFile()
    {
        $rates = [];
        if($this->shippingService->service_sub_class == ShippingService::Courier_Express){
            $limit = 110;
        }elseif($this->shippingService->service_sub_class == ShippingService::SRP){
            $limit = 75;
        }elseif($this->shippingService->service_sub_class == ShippingService::SRM){
            $limit = 7;
        }elseif($this->shippingService->service_sub_class == ShippingService::GePS){
            $limit = 27;
        }
        elseif($this->shippingService->service_sub_class == ShippingService::GePS_EFormat){
            $limit = 41;
        }elseif($this->shippingService->service_sub_class == ShippingService::Prime5){
            $limit = 33;
        }elseif($this->shippingService->service_sub_class == ShippingService::Post_Plus_Registered){
            $limit = 22;
        }elseif($this->shippingService->service_sub_class == ShippingService::Parcel_Post){
            $limit = 63;
        }else{
            $limit = 70;
        }
        foreach (range(3, $limit) as $row) {
            $rates[] = [
                'weight' => $this->workSheet->getCell('A'.$row)->getValue(),
                'leve' => round($this->workSheet->getCell('C'.$row)->getValue(),2)
            ];
        }

        return $this->storeRatesToDb($rates);
    }

    private function storeRatesToDb(array $data)
    {
        $rates = Rate::where('shipping_service_id',$this->shippingService->id)->first();
        
        if ( !$rates ){
            $rates= new Rate();
        }

        $rates->shipping_service_id = $this->shippingService->id;
        $rates->country_id = $this->countryId;
        $rates->data = $data;
        $rates->save();
        return $rates;
    }
}
