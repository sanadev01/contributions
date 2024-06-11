<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class LanguageController extends Controller
{
    public function __invoke($locale)
    {
        $user = User::find(auth()->user()->id);
        $user->locale = $locale;
        $user->save();

        return back();
    }
}
