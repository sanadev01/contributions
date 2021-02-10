<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\Admin\NewRegistration;
use App\Mail\User\AccountCreated;
use App\Models\Permission;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Rules\PhoneNumberValidator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required_if:account_type,'.User::ACCOUNT_TYPE_INDIVIDUAL],
            'phone' => ['required', new PhoneNumberValidator()],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'account_type' => 'required'
        ],[
            'phone.phone' => 'Invalid Phone number'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $locale = app()->getLocale();
        
        $user = User::create([
            'name' => $data['name'],
            'last_name' => isset($data['last_name']) ? $data['last_name'] : null,
            'phone' => $data['phone'],
            'role_id' => 2,
            'pobox_number' => User::generatePoBoxNumber(),
            'email' => $data['email'],
            'reffered_by' => User::findRef($data['reffered_by']),
            'reffer_code' => generateRandomString(),
            'password' => Hash::make($data['password']),
            'account_type' => $data['account_type'] == 'business' ? User::ACCOUNT_TYPE_BUSINESS : User::ACCOUNT_TYPE_INDIVIDUAL
        ]);


        saveSetting('locale', $locale, $user->id);
        return $user;
    }

    public function registered(Request $request, $user)
    {
        Mail::to($user->email)->send(new AccountCreated($user));
        Mail::send(new NewRegistration($user));
    }
    
}
