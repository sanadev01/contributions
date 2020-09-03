<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ProfitPackage;

class UserSettingController extends Controller
{
    public function index(User $user)
    {   
        $packages = ProfitPackage::all();
        return view('admin.users.setting.edit', compact('packages', 'user'));
    }

    public function store(Request $request, User $user){

        $this->validate($request,[
            'package_id' => 'required'
        ]);

        $user->update([
            'package_id' => $request->package_id
        ]);

        session()->flash('alert-success','User Setting Updated Successfully');
        return back();

    }

}
