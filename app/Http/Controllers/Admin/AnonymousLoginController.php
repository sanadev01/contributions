<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;


class AnonymousLoginController extends Controller
{
    public function __invoke(User $user)
    {
        session()->put('last_logged_in',Auth::user());
        Auth::login($user);
        return redirect()->route('admin.home');
    }
}
