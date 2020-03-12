<?php

namespace App\Http\Middleware;

use App\Models\SubjectRegister;
use Closure;
use Encore\Admin\Facades\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class TeacherMiddleware
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
        $idSubject = $request->route('id');
        $user = Admin::user();
        $idUser = $user->id;
        $arrIdSubjectRegister = SubjectRegister::where('id_user_teacher', $idUser)->pluck('id')->toArray();
        if(in_array($idSubject, $arrIdSubjectRegister)) {
            return $next($request);
        } else {
            $error = new MessageBag([
                'title'   => 'Cảnh báo',
                'message' => 'Bạn không có quyền truy cập',
            ]);
            return back()->with(compact('error'));
        }
    }
}
