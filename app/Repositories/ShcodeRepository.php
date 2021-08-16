<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Models\ShCode;
use Exception;

class ShcodeRepository
{
    public function get()
    {   
        $shCode = ShCode::all();
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

    public function update(Request $request,ShCode $role)
    {   
        
        try{
            
            $role->update([
                'name' => $request->role_name
            ]);

            return true;

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Role');
            return null;
        }
    }

    public function delete(ShCode $shcode)
    {

        $shcode->delete();
        return true;

    }

}