<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;


class SettingController extends Controller
{   
    public $adminId;

    public function __construct()
    {
        $this->adminId = \App\Models\User::ROLE_ADMIN;
        $this->authorizeResource(Setting::class);
    } 
       /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        $adminId = $this->adminId;
        return view('admin.settings.edit', compact('adminId'));
    } 

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        Setting::saveByKey('AUTHORIZE_ID', $request->AUTHORIZE_ID,null,true);
        Setting::saveByKey('AUTHORIZE_KEY', $request->AUTHORIZE_KEY,null,true);
        Setting::saveByKey('STRIPE_KEY', $request->STRIPE_KEY,null,true);
        Setting::saveByKey('STRIPE_SECRET', $request->STRIPE_SECRET,null,true);
        Setting::saveByKey('PAYMENT_GATEWAY', $request->PAYMENT_GATEWAY,null,true);
        Setting::saveByKey('TYPE', $request->TYPE,null,true);
        Setting::saveByKey('VALUE', $request->VALUE,null,true);

        $request->has('usps') ? saveSetting('usps', true, $this->adminId) : saveSetting('usps', false, $this->adminId);
        $request->has('ups') ? saveSetting('ups', true, $this->adminId) : saveSetting('ups', false, $this->adminId);
        $request->has('fedex') ? saveSetting('fedex', true, $this->adminId) : saveSetting('fedex', false, $this->adminId);

        ($request->usps_profit != null ) ? saveSetting('usps_profit', $request->usps_profit, $this->adminId) : saveSetting('usps_profit', 0, $this->adminId);
        ($request->ups_profit != null ) ? saveSetting('ups_profit', $request->ups_profit, $this->adminId) : saveSetting('ups_profit', 0, $this->adminId);
        ($request->fedex_profit != null ) ? saveSetting('fedex_profit', $request->fedex_profit, $this->adminId) : saveSetting('fedex_profit', 0, $this->adminId);

        session()->flash('alert-success', 'setting.Settings Saved');
        return back();
    }
}
