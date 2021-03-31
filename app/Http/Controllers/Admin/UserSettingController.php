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
        $users = User::user()->get();
        return view('admin.users.setting.edit', compact('packages', 'user', 'roles', 'users'));
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

        $ids = [];
        foreach($user->referrals as $referrer){
            array_push($ids,$referrer->id);
        }

        $newIds = [];
        if($request->referrer_id){
            foreach($request->referrer_id as $id){
                array_push($newIds,$id);
            if(!in_array($id, $ids)){
                User::find($id)->update([
                    'reffered_by' => $user->id
                    ]);
                }
            }
        }
        
        $diffence = array_diff($ids,$newIds);
        foreach($diffence as $id){
            User::find($id)->update([
                'reffered_by' => null
            ]);
        }

        session()->flash('alert-success','user.User Setting Updated Successfully');
        return back();
    }

}
