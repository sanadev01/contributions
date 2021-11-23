<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        if ( !Session::has('last_logged_in') ){
            $user = Auth::user();
            if ($user->isUser() && $user->status == 'suspended') {
                Auth::logout();

                session()->flash('alert-danger','Your Account has been suspended Please contact Us / Sua conta foi suspensa Entre em contato conosco');
                return redirect()->route('login');
            }
        }

        return view('home');   
    }

    public function testBrazilTracking()
    {
        Artisan::call('db:seed --class=RegionSeeder');
        Artisan::call('db:seed --class=CommuneSeeder');
        
        $output = Artisan::output();
        
        Log::info($output);

        return true;
    }
}
