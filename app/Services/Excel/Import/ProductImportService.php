<?php

namespace App\Services\Excel\Import;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\Excel\AbstractImportService;

class ProductImportService extends AbstractImportService
{
    private $userId;
    private $request;
    private $errors = [];

    public function __construct(UploadedFile $file,$request)
    {
        $this->userId = Auth::id();
        $this->request = $request;

        $filename = $this->importFile($file);

        parent::__construct(
            $this->getStoragePath($filename)
        );
    }

    public function handle()
    {
        $response = $this->importOrders();
         return $response;
    }

    public function importOrders()
    {
        try{
            foreach (range(2, $this->noRows) as $row) {
                $this->createOrUpdateProduct($row);
            }
            return true;
        } catch (\Exception $ex) {
            DB::rollback();
            return $ex->getMessage();
        }
    }

    private function createOrUpdateProduct($row)
    {
        DB::beginTransaction();
        
        try {
            
            $product = Product::where('sku',$this->getValue("C{$row}"))->first();
            if ($product || strlen($this->getValue("C{$row}")) <=0 || strlen($this->getValue("K{$row}")) <=0 ){
                return;
            }
            $value = preg_replace("/[^0-9.]/", "", $this->getValue("B{$row}"));
            $order = Product::create([
                'user_id'       => Auth::user()->isAdmin() ? $this->request->user_id : $this->userId,
                'name'          => $this->getValue("A{$row}"),
                'price'         =>  $value,
                'sku'           => $this->getValue("C{$row}"),
                'status'        => $this->getValue("D{$row}"),
                'order'         => $this->getValue("E{$row}"),
                'category'      => $this->getValue("F{$row}"),
                'brand'         => $this->getValue("G{$row}"),
                'manufacturer'  => $this->getValue("H{$row}"),
                'barcode'       => $this->getValue("I{$row}"),
                'description'   => $this->getValue("J{$row}"),
                'quantity'      => $this->getValue("K{$row}"),
                'item'          => $this->getValue("L{$row}"),
                'lot'           => $this->getValue("M{$row}"),
                'unit'          => $this->getValue("N{$row}"),
                'case'          => $this->getValue("O{$row}"),
                'inventory_value'=>  $value*$this->getValue("K{$row}"),
                'min_quantity'  => $this->getValue("P{$row}"),
                'max_quantity'  => $this->getValue("Q{$row}"),
                'discontinued'  => $this->getValue("R{$row}"),
                'store_day'     => $this->getValue("S{$row}"),
                'location'      => $this->getValue("T{$row}"),
                'sh_code'      => is_numeric($this->getValue("U{$row}"))?$this->getValue("U{$row}"):null,
                'weight'       => $this->getValue("V{$row}"),
                'measurement_unit' => $this->getValue("W{$row}"),
                'exp_date' => $this->getValue("X{$row}")?date('Y-m-d H:i:s', strtotime($this->getValue("X{$row}"))):null,
            ]);
            DB::commit();
            return $order;

        } catch (\Exception $ex) {
            DB::rollback();
            return $ex->getMessage();
        }
    }

    // this function returns all validation errors after import:
    public function getErrors()
    {
        return $this->errors;
    }

}
