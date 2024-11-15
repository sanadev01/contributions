<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\UserLoginDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        // $orders = $dashboard->getDashboardStats();
        return view('home');
    }
    function loginDetails()
    {

        $userRisk = UserLoginDetail::with('user')
            ->select(
                'user_id',
                DB::raw('COUNT(DISTINCT ip_address) as unique_ips'),
                DB::raw('COUNT(DISTINCT device) as unique_devices'),
                DB::raw('COUNT(*) as total_attempts'),
                DB::raw('SUM(successful) as successful_attempts')
            )->when(!Auth::user()->isAdmin(),function($query){
                $query->where('user_id',Auth::user()->id);
            })
            ->groupBy('user_id')
            ->paginate(10);
        foreach ($userRisk as $detail) {
            $unsuccessfulAttempts = $detail->total_attempts - $detail->successful_attempts;
            $uniqueIps = $detail->unique_ips;
            $uniqueDevices = $detail->unique_devices;

            if ($unsuccessfulAttempts > 5 || $uniqueIps > 3 || $uniqueDevices > 3) {
                $detail->risk = 'High';
            } elseif ($unsuccessfulAttempts   > 2 || $uniqueIps > 2 || $uniqueDevices > 2) {
                $detail->risk = 'Medium';
            } else {
                $detail->risk = 'Low';
            }
        }



        $loginDetails = UserLoginDetail::with('user')->whereHas('user',function($query){
            $query->when(!Auth::user()->isAdmin(),function($query){
                $query->where('user_id',Auth::user()->id);
            });
        })->paginate(10); 
        return view('login_details', compact('loginDetails', 'userRisk'));
    }
}
