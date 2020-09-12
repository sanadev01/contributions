<?php

namespace App\Http\Middleware;
use App\Models\User;
use Auth;
use App;

use Closure;

class LanguageSwitcher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        if (Auth::check()) {
            $user = User::find(auth()->user()->id);
            App::setLocale($user->locale);
        }else{
            App::setLocale('en');
        }

        return $next($request);
    }
}
