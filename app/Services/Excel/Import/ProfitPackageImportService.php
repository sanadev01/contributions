<?php

namespace App\Services\Excel\Import;

use App\Models\Country;
use App\Models\Order;
use App\Models\ProfitPackage;
use App\Models\ShippingService;
use App\Models\State;
use App\Services\Excel\AbstractImportService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ProfitPackageImportService extends AbstractImportService
{
    private $userId; 
    private $request; 

    public function __construct(UploadedFile $file,$userId, $request, $profitPackage = null)
    {
        $this->userId = $userId;
        $this->request = $request;
        $this->profitPackage = $profitPackage;

        $filename = $this->importFile($file);


        parent::__construct(
            $this->getStoragePath($filename)
        );
        
    }

    public function handle() 
    {
        $data = $this->importProfitPackage();
        
        $this->createOrUpdateOrder($data);
       
    }

    public function importProfitPackage()
    {
        $data   = [];
        $arrayCounter = 0;

        foreach (range(2, $this->noRows) as $row) {
            $weight = preg_replace("/[^0-9.]/", "", $this->getValue("A{$row}"));
            $value = preg_replace("/[^0-9.]/", "", $this->getValue("C{$row}"));
            
            \Log::info("weight : {$weight}, Value: {$value}");
            if($arrayCounter == 0){
                $data[ $arrayCounter ]['min_weight'] = 0;
            }else{
                if($weight){
                    $minWeight = $arrayCounter - 1;
                    $data[ $arrayCounter ]['min_weight'] = $data[ $minWeight ]['max_weight'] + 1;
                }
            }
            if($weight){
                $data[ $arrayCounter ]['max_weight'] = $weight;
                $data[ $arrayCounter ]['value'] = $value;
                $arrayCounter ++;
            }
        }
        return $data;

    }

    private function createOrUpdateOrder($data){
        
        DB::beginTransaction();
        
        try {
            if($this->profitPackage){
                $this->profitPackage->update([
                    "shipping_service_id" => $this->request->shipping_service_id,
                    "name" => $this->request->package_name,
                    "type" => $this->request->type,
                    "data" => $data,
                ]);
                
            }else{

                $profitPackage = ProfitPackage::create([
                    "shipping_service_id" => $this->request->shipping_service_id,
                    "name" => $this->request->package_name,
                    "type" => $this->request->type,
                    "data" => $data,
                ]);
            }

            DB::commit();

        } catch (\Exception $ex) {
            DB::rollback();
            \Log::info($ex->getMessage());
        }
    }


}
