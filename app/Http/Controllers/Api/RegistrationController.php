<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegistrationController extends Controller
{

    protected $register;

    public function __construct(RegisterController $register)
    {
        $this->register = $register;
    }


    public function __invoke(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'min:10', 'max:15'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => 'required| min:6|confirmed ',
            'password_confirmation' => 'required| min:6'
        ], [
            'phone.phone' => 'Invalid Phone number'
        ]);

        $user = $this->register->registerUser($request->toArray() + ['name' => $request->first_name, 'come_from' => ($request->come_from ?? '')]);
        if ($user)
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'last_name' => $user->last_name,
                'pobox_number' => $user->pobox_number,
                'email' => $user->email,
                'api_token' => $user->api_token,
            ]);
        else
            return responseUnprocessable('unable to register user');
    }
}
