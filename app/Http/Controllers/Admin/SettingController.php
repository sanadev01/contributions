<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;


class SettingController extends Controller
{   
    public function __construct()
    {
        $this->authorizeResource(Setting::class);
    } 
       /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        return view('admin.settings.edit');
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

        session()->flash('alert-success', 'setting.Settings Saved');
        return back();
    }
}
