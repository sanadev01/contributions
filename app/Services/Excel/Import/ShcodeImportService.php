<?php

namespace App\Services\Excel\Import;

use App\Models\ShCode;
use Illuminate\Http\UploadedFile;
use App\Services\Excel\AbstractImportService;

class ShcodeImportService extends AbstractImportService
{

    private $errors = [];

    public function __construct(UploadedFile $file, $request)
    {


        $filename = $this->importFile($file);

        parent::__construct(
            $this->getStoragePath($filename)
        );
    }

    public function handle()
    {
        $response = $this->importShcode();
        return $response;
    }

    public function importShcode()
    {
        try {
            foreach (range(2, $this->noRows) as $row) {
                if(!ShCode::where('code', $this->getValue("A{$row}"))->where('type', $this->getValue("E{$row}"))->first()){
                    $res = $this->storeShcode($row);
                    \Log::info($res);
                }
            }
        } catch (\Exception $ex) {

            return $ex->getMessage();
        }
    }

    private function storeShcode($row)
    {   
        try {
            if (strlen($this->getValue("A{$row}")) <=0 ){
                return;
            }
            $shcode = ShCode::updateOrCreate(
                [
                'code' => $this->getValue("A{$row}"),
                'type'   => $this->getValue("E{$row}") ?? null,
                ],
                [
                    'description'   => $this->getValue("B{$row}") . '-------' . $this->getValue("C{$row}") . '-------' . $this->getValue("D{$row}"),
                ]);
            return $shcode;
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    // this function returns all errors after import:
    public function getErrors()
    {
        return $this->errors;
    }
}
