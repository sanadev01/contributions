<?php
namespace App\Repositories;

use App\Models\User;
use App\Models\Deposit;
use App\Models\PaymentInvoice;
use App\Models\CommissionSetting;

class UserSettingRepository {

    public function store($request, $user){
        $user->update([
            'package_id' => $request->package_id,
            'role_id' => $request->role_id,
            'status' => $request->status,
            'api_enabled' => $request->has('api_enabled'),
            'market_place_name' => $request->market_place_name,
            'email' => $request->user_email,
            'amazon_api_enabled' => $request->has('amazon_api_enabled'),
            'amazon_api_key' => $request->amazon_api_key,
        ]);

        $request->has('battery') ? saveSetting('battery', true, $user->id) : saveSetting('battery', false, $user->id);
        $request->has('perfume') ? saveSetting('perfume', true, $user->id) : saveSetting('perfume', false, $user->id);
        $request->has('insurance') ? saveSetting('insurance', true, $user->id) : saveSetting('insurance', false, $user->id);
        $request->has('usps') ? saveSetting('usps', true, $user->id) : saveSetting('usps', false, $user->id);
        $request->has('ups') ? saveSetting('ups', true, $user->id) : saveSetting('ups', false, $user->id);
        $request->has('stripe') ? saveSetting('stripe', true, $user->id) : saveSetting('stripe', false, $user->id);
        $request->has('sinerlog') ? saveSetting('sinerlog', true, $user->id) : saveSetting('sinerlog', false, $user->id);
        $request->has('fedex') ? saveSetting('fedex', true, $user->id) : saveSetting('fedex', false, $user->id);
        $request->has('volumetric_discount') ? saveSetting('volumetric_discount', true,$user->id) : saveSetting('volumetric_discount', false, $user->id);

        ($request->usps_profit != null ) ? saveSetting('usps_profit', $request->usps_profit, $user->id) : saveSetting('usps_profit', 0, $user->id);
        ($request->ups_profit != null ) ? saveSetting('ups_profit', $request->ups_profit, $user->id) : saveSetting('ups_profit', 0, $user->id);
        ($request->discount_percentage != null ) ? saveSetting('discount_percentage', $request->discount_percentage, $user->id) : saveSetting('discount_percentage', 0, $user->id);
        ($request->fedex_profit != null ) ? saveSetting('fedex_profit', $request->fedex_profit, $user->id) : saveSetting('fedex_profit', 0, $user->id);
        
        ($request->weight != null ) ? saveSetting('weight', $request->weight, $user->id) : saveSetting('weight', 0, $user->id);
        ($request->length != null ) ? saveSetting('length', $request->length, $user->id) : saveSetting('length', 0, $user->id);
        ($request->width != null ) ? saveSetting('width', $request->width, $user->id) : saveSetting('width', 0, $user->id);
        ($request->height != null ) ? saveSetting('height', $request->height, $user->id) : saveSetting('height', 0, $user->id);

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
        return true;
    }
}