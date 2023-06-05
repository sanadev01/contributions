<?php

namespace App\Repositories;

use Exception;
use App\Models\ShCode;
use Illuminate\Http\Request;
use App\Services\Excel\Import\ShcodeImportService;

class ShcodeRepository
{
    public function get()
    {   
        $sixDigitCodes = ShCode::whereRaw('LENGTH(code) = 6')->orderBy('description','ASC')->get()->toArray();
        $tenDigitCodes = ShCode::whereRaw('LENGTH(code) = 10')->orderBy('description','ASC')->get()->toArray();
        $shCode = array_merge($sixDigitCodes, $tenDigitCodes);
        return $shCode;

    }

    public function store(Request $request)
    {   
        try{

            ShCode::create([
                'code' => $request->code,
                'description' => $request->en.'-------'.$request->pt.'-------'.$request->sp,
            ]);

            return true;

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Saving Shcode');
            return null;
        }
    }

    public function update(Request $request,ShCode $shcode)
    {   
        
        try{
            
            $shcode->update([
                'code' => $request->code,
                'description' => $request->en.'-------'.$request->pt.'-------'.$request->sp,
            ]);

            return true;

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while shcode');
            return null;
        }
    }

    public function delete(ShCode $shcode)
    {

        $shcode->delete();
        return true;

    }
    
    public function fileImport(Request $request)
    {
        $importExcelService = new ShcodeImportService($request->file('file'),$request);
        $importShcode = $importExcelService->handle();
        return $importShcode;
    }

}