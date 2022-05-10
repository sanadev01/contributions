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
        // Setting::saveByKey('STRIPE_KEY', $request->STRIPE_KEY,null,true);
        // Setting::saveByKey('STRIPE_SECRET', $request->STRIPE_SECRET,null,true);
        Setting::saveByKey('PAYMENT_GATEWAY', $request->PAYMENT_GATEWAY,null,true);
        Setting::saveByKey('TYPE', $request->TYPE,null,true);
        Setting::saveByKey('VALUE', $request->VALUE,null,true);

        ($request->correios_setting == 'anjun_api') ? saveSetting('anjun_api', true, $this->adminId) : saveSetting('anjun_api', false, $this->adminId);
        
        session()->flash('alert-success', 'setting.Settings Saved');
        return back();
    }
}
