<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;


class SettingController extends Controller
{
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

        session()->flash('alert-success', 'Settings Saved');
        return back();
    }
}
