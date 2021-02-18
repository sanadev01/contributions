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

    public function store(Request $request, User $user)
    {
        $this->validate($request,[
            'user_email' => 'required|unique:users,email,'.$user->id,
            'password' => 'nullable|min:8',
        ]);
        
        $user->update([
            'package_id' => $request->package_id,
            'role_id' => $request->role_id,
            'api_enabled' => $request->has('api_enabled'),
            'market_place_name' => $request->market_place_name,
            'email' => $request->user_email,
            'battery' => $request->has('battery'),
            'perfume' => $request->has('perfume')
        ]);

        if ( $request->password ){
            $user->update([
                'password' => bcrypt($request->password)
            ]);
        }

        session()->flash('alert-success','user.User Setting Updated Successfully');
        return back();
    }

}
