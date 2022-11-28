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
        $shCode = ShCode::orderBy('description','ASC')->get();
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