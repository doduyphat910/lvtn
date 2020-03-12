<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class StudentMiddleware
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
//        if (Auth::guard('web')->check()) {
//            Config::set('activitylog.default_auth_driver', 'web');
//        }
        if(Auth::check()){
                return $next($request);
        } else {
            return redirect('getLogin');
        }
    }
}
