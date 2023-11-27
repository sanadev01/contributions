<?php

namespace App\Http\Controllers\Api; 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Mail\User\AccountCreated;
use Illuminate\Support\Facades\Mail;
use App\Mail\Admin\NewRegistration;
class RegistrationController extends Controller
{

    public function __invoke(Request $request)
    { 
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'min:10', 'max:15'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'], 
            'password' => 'required| min:4|confirmed ',
            'password_confirmation' => 'required| min:4'
        ],[
            'phone.phone' => 'Invalid Phone number'
        ]);


        $user = User::create([
            'name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'role_id' => 2,
            'pobox_number' => User::generatePoBoxNumber(),
            'email' => $request->email,
            // 'reffered_by' => ($referrer) ? $referrer->id : null,
            'reffer_code' => generateRandomString(),
            // 'come_from' => $data['come_from'],
            'api_token' => md5(microtime()).'-'.Str::random(116).'-'.md5(microtime()),
            'api_enabled'=>true,
            'password' => Hash::make($request->password),
            // 'account_type' => $data['account_type'] == 'business' ? User::ACCOUNT_TYPE_BUSINESS : User::ACCOUNT_TYPE_INDIVIDUAL
        ]); 
        Mail::to($user->email)->send(new AccountCreated($user));
        Mail::send(new NewRegistration($user));
        return response()->json([
            'id'=>$user->id,
            'name'=>$user->name,
            'last_name'=>$user->last_name,
            'pobox_number'=>$user->pobox_number,
            'email'=>$user->email,
            'api_token'=>$user->api_token, 
        ]);
    }
}