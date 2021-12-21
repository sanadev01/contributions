<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use App\Models\Deposit;
use Illuminate\Http\Request;
use App\Models\ProfitPackage;
use App\Models\PaymentInvoice;
use App\Models\CommissionSetting;
use App\Http\Controllers\Controller;

class UserSettingController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class);
    }
    
    public function index(User $user)
    {   
        // saveSetting('ups', true, $user->id);
        // dd(setting('ups', null, $user->id));
        $packages = ProfitPackage::orderBy('name','ASC')->get();
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
            'status' => $request->status,
            'api_enabled' => $request->has('api_enabled'),
            'market_place_name' => $request->market_place_name,
            'email' => $request->user_email,
            'battery' => $request->has('battery') ? saveSetting('perfume', true, $user->id) : saveSetting('battery', false, $user->id),
            'perfume' => $request->has('perfume') ? saveSetting('perfume', true, $user->id) : saveSetting('perfume', false, $user->id),
            'insurance' => $request->has('insurance') ? saveSetting('insurance', true, $user->id) : saveSetting('insurance', false, $user->id),
            'usps' => $request->has('usps') ? saveSetting('usps', true, $user->id) : saveSetting('usps', false, $user->id),
            'ups' => $request->has('ups') ? saveSetting('ups', true, $user->id) : saveSetting('ups', false, $user->id),
            'stripe' => $request->has('stripe') ? saveSetting('stripe', true, $user->id) : saveSetting('stripe', false, $user->id),
            'sinerlog' => $request->has('sinerlog') ? saveSetting('sinerlog', true, $user->id) : saveSetting('sinerlog', false, $user->id),
            'api_profit' => $request->input('api_profit'),
            'order_dimension' => $request->input('order_dimension'),
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

            $commissionSetting = CommissionSetting::where('user_id', $user->id)->where('referrer_id', $id)->first();
            if($commissionSetting){
                $commissionSetting->delete();
            }
        }

        if($request->status == 'suspended'){
            $lastTransaction = Deposit::where('user_id', $user->id)->latest('id')->first();
            if($lastTransaction){
                if($lastTransaction->balance > 0){
                    $deposit = Deposit::create([
                        'uuid' => PaymentInvoice::generateUUID('DP-'),
                        'amount' => $lastTransaction->balance,
                        'user_id' => $user->id,
                        'last_four_digits' => 'Account Suspended',
                        'balance' => $lastTransaction->balance - $lastTransaction->balance,
                        'is_credit' => false,
                    ]);
                }
            }
            $user->update([
                'status' => $request->status,
                'api_enabled' => false
            ]);
        }

        session()->flash('alert-success','user.User Setting Updated Successfully');
        return back();
    }

}
