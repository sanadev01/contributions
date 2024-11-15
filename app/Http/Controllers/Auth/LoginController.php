<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session; 
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCode;
use Illuminate\Foundation\Auth\ThrottlesLogins;  
use Illuminate\Validation\ValidationException;
class LoginController extends Controller
{
    use ThrottlesLogins;
 
    protected $maxAttempts = 5;
    protected $decayMinutes = 30;   
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {  

        $this->validateLogin($request);
        if ($this->hasTooManyLoginAttempts($request)){
            $this->fireLockoutEvent($request);
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.throttle', ['minutes' =>$this->decayMinutes])],
            ]);
        }

        if (Auth::attempt($this->credentials($request))) {
            $this->clearLoginAttempts($request);
            return $this->authenticated($request,Auth::user());
        }
        $this->incrementLoginAttempts($request);
        $attempts = $this->limiter()->attempts($this->throttleKey($request));
        $remainingAttempts = $this->maxAttempts - $attempts;
        if ($remainingAttempts === 1) {
            throw ValidationException::withMessages([
                $this->username() => ['Warning: This is your last attempt before lockout.'],
            ]);
        }

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    protected function authenticated(Request $request, $user)
    {
        if($user->email=='admin@admin.com'){ 
            return redirect('/dashboard');
        }
        $token = $user->generateVerificationToken();
        Auth::logout();
        Mail::to($user->email)->send(new TwoFactorCode($token)); 
        $request->session()->put('auth_user_id', $user->id);
        $request->session()->put('verification_attempts',0);


        return redirect()->route('showVerificationForm');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {

        if ( Session::has('last_logged_in') ){
            Auth::login(
                Session::get('last_logged_in')
            );


            Session::forget('last_logged_in');

            return redirect()->route('admin.users.index');
        }


        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new Response('', 204)
            : redirect('/');
    }
}
