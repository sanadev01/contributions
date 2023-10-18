<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Mail\Admin\SettingUpdate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;


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
        if(auth()->user()->id == 1 ){
            $adminId = $this->adminId;
            return view('admin.settings.edit', compact('adminId'));
        }
        abort(403, 'Unauthorized action.');
    } 

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $userData = [
            'TYPE' => setting('TYPE'),
            'VALUE' => setting('VALUE'),
            'usps' => setting('usps', null, $this->adminId)? 'Active': 'Inactive',
            'ups' => setting('ups', null, $this->adminId)? 'Active': 'Inactive',
            'fedex' => setting('fedex', null, $this->adminId)? 'Active': 'Inactive',
            'usps_profit'=> setting('usps_profit', null, $this->adminId) ? setting('usps_profit', null, $this->adminId): 0,
            'ups_profit'=> setting('ups_profit', null, $this->adminId) ?setting('ups_profit', null, $this->adminId) : 0,
            'fedex_profit'=> setting('fedex_profit', null, $this->adminId)? setting('fedex_profit', null, $this->adminId): 0,
            'AUTHORIZE_ID'=> setting('AUTHORIZE_ID'),
            'AUTHORIZE_KEY'=> setting('AUTHORIZE_KEY'),
            'correios_setting'=> setting('anjun_api', null, $this->adminId) ? 'Correios Anjun API' : (setting('china_anjun_api', null, $this->adminId)?'China Anjun':(setting('bcn_api', null, $this->adminId)?'BCN Setting':'Correios API')),
        ];
        try {
            \Mail::send(new SettingUpdate($user, $request, $userData, true));
        } catch (\Exception $ex) {
            \Log::info('Setting Update email send error: '.$ex->getMessage());
        }
        Setting::saveByKey('AUTHORIZE_ID', $request->AUTHORIZE_ID,null,true);
        Setting::saveByKey('AUTHORIZE_KEY', $request->AUTHORIZE_KEY,null,true);
        // Setting::saveByKey('STRIPE_KEY', $request->STRIPE_KEY,null,true);
        // Setting::saveByKey('STRIPE_SECRET', $request->STRIPE_SECRET,null,true);
        Setting::saveByKey('PAYMENT_GATEWAY', $request->PAYMENT_GATEWAY,null,true);
        Setting::saveByKey('TYPE', $request->TYPE,null,true);
        Setting::saveByKey('VALUE', $request->VALUE,null,true);

        
        //switch 3 api for correies/anjun standerd/express api.
        saveSetting('bcn_api', false, $this->adminId);
        saveSetting('china_anjun_api', false, $this->adminId);
        saveSetting('correios_api', false, $this->adminId);
        saveSetting('anjun_api', false, $this->adminId);
        saveSetting($request->correios_setting, true, $this->adminId);
        
        $request->has('usps') ? saveSetting('usps', true, $this->adminId) : saveSetting('usps', false, $this->adminId);
        $request->has('ups') ? saveSetting('ups', true, $this->adminId) : saveSetting('ups', false, $this->adminId);
        $request->has('fedex') ? saveSetting('fedex', true, $this->adminId) : saveSetting('fedex', false, $this->adminId);
        $request->has('geps_service') ? saveSetting('geps_service', true, $this->adminId) : saveSetting('geps_service', false, $this->adminId);
        $request->has('sweden_post') ? saveSetting('sweden_post', true, $this->adminId) : saveSetting('sweden_post', false, $this->adminId);
        $request->has('post_plus') ? saveSetting('post_plus', true, $this->adminId) : saveSetting('post_plus', false, $this->adminId);
        $request->has('gss') ? saveSetting('gss', true, $this->adminId) : saveSetting('gss', false, $this->adminId);
        $request->has('gde') ? saveSetting('gde', true, $this->adminId) : saveSetting('gde', false, $this->adminId);

        ($request->usps_profit != null ) ? saveSetting('usps_profit', $request->usps_profit, $this->adminId) : saveSetting('usps_profit', 0, $this->adminId);
        ($request->ups_profit != null ) ? saveSetting('ups_profit', $request->ups_profit, $this->adminId) : saveSetting('ups_profit', 0, $this->adminId);
        ($request->fedex_profit != null ) ? saveSetting('fedex_profit', $request->fedex_profit, $this->adminId) : saveSetting('fedex_profit', 0, $this->adminId);
        ($request->gss_profit != null ) ? saveSetting('gss_profit', $request->gss_profit, $this->adminId) : saveSetting('gss_profit', 0, $this->adminId);
        ($request->gde_pm_profit != null ) ? saveSetting('gde_pm_profit', $request->gde_pm_profit, $this->adminId) : saveSetting('gde_pm_profit', 0, $this->adminId);
        ($request->gde_fc_profit != null ) ? saveSetting('gde_fc_profit', $request->gde_fc_profit, $this->adminId) : saveSetting('gde_fc_profit', 0, $this->adminId);

        session()->flash('alert-success', 'setting.Settings Saved');
        return back();
    }
}
