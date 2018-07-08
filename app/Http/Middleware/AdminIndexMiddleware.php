<?php

namespace App\Http\Middleware;

use Closure;
use Encore\Admin\Facades\Admin;

class AdminIndexMiddleware
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
        $roles = Admin::user()->roles->first();
        if($roles->slug == 'giangvien') {
            return redirect('admin/teacher/class');
        } else {
            return $next($request);
        }
    }
}
