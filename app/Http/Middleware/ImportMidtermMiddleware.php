<?php

namespace App\Http\Middleware;
use Illuminate\Support\MessageBag;
use App\Models\ResultRegister;
use App\Models\TimeRegister;
use Closure;

class ImportMidtermMiddleware
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
        $idSubjectRegister = $request->route('id');
        $idTimeRegister = ResultRegister::where('id_subject_register', $idSubjectRegister)->pluck('time_register');
        $statusImport = TimeRegister::find($idTimeRegister)->first();
        $statusImport = $statusImport->status_import;
        if (in_array('2', $statusImport) ) {
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
