<?php

namespace App\Services\Excel\Import;

use App\Models\ShCode;
use App\Services\Excel\AbstractImportService;
use Illuminate\Database\Eloquent\Model;

class NCMImportService extends AbstractImportService
{
    public function __construct()
    {
        parent::__construct(
            storage_path('app/excels/ncm.xlsx')
        );
    }

    public function handle() 
    {
        $this->importShCodes();
    }

    public function importShCodes()
    {
        $shCodes = collect();

        foreach (range(2, 1653) as $row) {
            if ( $this->workSheet->getCell('B'.$row)->getValue() == 'SIM' ) continue;
            
            $shCodes->push([
                'code' => $this->workSheet->getCell('A'.$row)->getValue(),
                'description' => $this->getDescription($row),
                'chapter' => $this->workSheet->getCell('D'.$row)->getValue()
            ]);
        }

        foreach($shCodes->chunk(100) as $shCodesChunk){
            ShCode::insert($shCodesChunk->toArray());
        }


    }

    private function getDescription($row){
        $description = "";

        $description .= $this->workSheet->getCell('F'.$row)->getValue();
        $description .= $this->workSheet->getCell('E'.$row)->getValue();
        $description .= $this->workSheet->getCell('D'.$row)->getValue();
        $description .= $this->workSheet->getCell('C'.$row)->getValue();

        return $description;
    }
}
