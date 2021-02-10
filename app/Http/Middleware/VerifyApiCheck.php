<?php

namespace App\Http\Middleware;

use App\Models\ApiLog;
use Closure;
use Illuminate\Support\Facades\Auth;

class VerifyApiCheck
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
        if ( !optional(Auth::user())->api_enabled ){
            abort(403,"Unauthorized");
        }
        
        ApiLog::create([
            'user_id' => \Auth::id(),
            'type' => ApiLog::TYPE_WHOLE_SALE,
            'data' => $request->all()
        ]);

        return $next($request);
    }
}
