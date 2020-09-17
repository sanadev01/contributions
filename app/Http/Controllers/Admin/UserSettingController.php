<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ProfitPackage;
use App\Models\Role;

class UserSettingController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class);
    }
    
    public function index(User $user)
    {   
        $packages = ProfitPackage::all();
        $roles = Role::orderBy('id', 'desc')->get();
        return view('admin.users.setting.edit', compact('packages', 'user', 'roles'));
    }

    public function store(Request $request, User $user){

        $user->update([
            'package_id' => $request->package_id,
            'role_id' => $request->role_id,
            'api_enabled' => $request->has('api_enabled')
        ]);

        session()->flash('alert-success','user.User Setting Updated Successfully');
        return back();
    }

}
