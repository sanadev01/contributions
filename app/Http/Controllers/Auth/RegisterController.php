<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\Admin\NewRegistration;
use App\Mail\User\AccountCreated;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\CommissionSetting;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
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
    protected $redirectTo = 'login';

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
            'last_name' => ['sometimes', 'required_if:account_type,' . User::ACCOUNT_TYPE_INDIVIDUAL],
            'phone' => ['required', 'min:10', 'max:15'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'string',
                'min:12',
                'confirmed',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&^#()_+={}\[\]|:;,.<>~`]/',
                function ($attribute, $value, $fail) use ($data) {
                    if((isset($data['name']) && is_string($data['name']) && stripos($value, $data['name']) !== false)) {
                        $fail('The password cannot contain your first name.');
                    }
                    if((isset($data['last_name']) && is_string($data['last_name']) && stripos($value, $data['last_name']) !== false)) {
                        $fail('The password cannot contain your last name.');
                    }
                }
            ],
            'account_type' => 'required'
        ], [
            'phone.phone' => 'Invalid Phone number',
            'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.',

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

        $referrer = User::findRef($data['reffered_by']);

        $user = User::create([
            'name' => $data['name'],
            'last_name' => isset($data['last_name']) ? $data['last_name'] : null,
            'phone' => $data['phone'],
            'role_id' => 2,
            'pobox_number' => User::generatePoBoxNumber(),
            'email' => $data['email'],
            'reffered_by' => ($referrer) ? $referrer->id : null,
            'reffer_code' => generateRandomString(),
            'come_from' => $data['come_from'],
            'password' => Hash::make($data['password']),
            'account_type' => $data['account_type'] == 'business' ? User::ACCOUNT_TYPE_BUSINESS : User::ACCOUNT_TYPE_INDIVIDUAL
        ]);

        if ($user->reffered_by) {
            $this->setReferererCommission($user, $referrer);
        }
        saveSetting('locale', $locale, $user->id);
        saveSetting('geps_service', true, $user->id);
        return $user;
    }
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        // $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
                    ? new JsonResponse([], 201)
                    : redirect($this->redirectPath());
    }
    public function registered(Request $request, $user)
    {
        Mail::to($user->email)->send(new AccountCreated($user));
        Mail::send(new NewRegistration($user));
    }

    private function setReferererCommission($user, $referrer)
    {
        if ($referrer) {
            CommissionSetting::create([
                'user_id' => $referrer->id,
                'referrer_id' => $user->id,
                'type' => 'flat',
                'value' => 0.1,
            ]);
        }

        return true;
    }
}
