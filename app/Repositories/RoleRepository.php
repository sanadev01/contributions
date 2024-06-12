<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use Exception;

class RoleRepository
{
    public function get()
    {   
        $roles = Role::all();
        return $roles;

    }

    public function store(Request $request)
    {   
        try{

            Role::create([
                'name' => $request->role_name
            ]);

            return true;

        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Saving Role');
            return null;
        }
    }

    public function update(Request $request,Role $role)
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

    public function delete(Role $role){

        $role->permissions()->delete();
        $role->delete();
        return true;

    }

}