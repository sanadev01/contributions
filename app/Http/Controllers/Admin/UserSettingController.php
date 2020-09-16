<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ProfitPackage;
use App\Models\Role;

class UserSettingController extends Controller
{
    public function index(User $user)
    {   
        $packages = ProfitPackage::all();
        $roles = Role::orderBy('id', 'desc')->get();
        return view('admin.users.setting.edit', compact('packages', 'user', 'roles'));
    }

    public function store(Request $request, User $user){

        $this->validate($request,[
            'package_id' => 'required'
        ]);

        if($request->has('api_enabled')) {
            
            $user->update([
                'api_enabled' => 1
            ]);

        }else{

            $user->update([
                'api_enabled' => 0
            ]);

        }

        $user->update([
            'package_id' => $request->package_id,
            'role_id' => $request->role_id,
        ]);

        session()->flash('alert-success','user.User Setting Updated Successfully');
        return back();

    }

}
