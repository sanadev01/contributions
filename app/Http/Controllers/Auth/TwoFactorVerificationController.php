<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorVerificationController extends Controller
{
    

    public function showVerificationForm()
    {
         
        $userId = session()->get('auth_user_id');
         if (!$userId) {
            session()->flash('alert-danger','Time expired. Please log in again.');
            return redirect()->route('login');
        }
        $user = User::find($userId);
        $remainingTime = $user->remainingTime(); 
        if ($remainingTime>0){ 
            return view('auth.auth_verify',['remainingTime'=>$remainingTime]); 
        } 
        session()->flash('alert-danger','Time expired. Please log in again.');

        return redirect()->route('login');


    }

    public function verifyToken(Request $request)
    {
        $token=implode('', $request->input('token'));
        $userId = $request->session()->get('auth_user_id');
        if (!$userId) {
            session()->flash('alert-danger','Session expired. Please log in again.');
            return redirect()->route('login');
        }

        $user = User::find($userId);
        $attempts = $request->session()->get('verification_attempts', 0);
        if ($attempts >= 3) {
            session()->flash('alert-danger', 'Too many attempts. Please request a new token.');
            return redirect()->route('login')->withErrors(['messages' => 'You have exceeded the maximum number of attempts.']);
        }
        if ($user && $user->verifyToken($token)) {
            Auth::login($user);
            $request->session()->forget('auth_user_id'); // Clear the session user ID
            return redirect()->intended('home');
        }
        $request->session()->put('verification_attempts', $attempts + 1);

        session()->flash('alert-danger',"Given code $token is invalid.Please try again."); 
        return redirect()->back()->withErrors(['messages' => 'Invalid or expired token']);
    }
}
