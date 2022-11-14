<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ProfitPackage;
use App\Http\Controllers\Controller;
use App\Repositories\UserSettingRepository;

class UserSettingController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class);
    }
    
    public function index(User $user)
    {   
        $packages = ProfitPackage::orderBy('name','ASC')->get();
        $roles = Role::orderBy('id', 'desc')->get();
        $users = User::user()->get();
        return view('admin.users.setting.edit', compact('packages', 'user', 'roles', 'users'));
    }

    public function store(Request $request, User $user,UserSettingRepository $userSettingRepository)
    {
        $this->validate($request,[
            'user_email' => 'required|unique:users,email,'.$user->id,
            'password' => 'nullable|min:8',
        ]);
        
        $userSettingRepository->store($request, $user);
        session()->flash('alert-success','user.User Setting Updated Successfully');
        return back();
    }

}
