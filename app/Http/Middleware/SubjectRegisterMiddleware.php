<?php

namespace App\Http\Middleware;

use App\Http\Extensions\Facades\User;
use App\Models\Status;
use App\Models\TimeRegister;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class SubjectRegisterMiddleware
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
        $timeRegister = TimeRegister::where('status', 1)->orderBy('id', 'DESC')->first();
        $user = Auth::user();
        $statusUser = $user->id_status;
        $nameStatus = Status::where('ids', $statusUser)->pluck('status')->toArray();
        //lấy năm vào học của user
        $schoolYearUser = $user->school_year;
        $schoolYearUser = (string) $schoolYearUser;
        if($timeRegister){
            //xét user có nằm trong đợt đăng kí hay không
        if(in_array($schoolYearUser, $timeRegister->school_year) || $timeRegister->school_year['0'] == "All")
         {
            //xét trạng thái user
            if($statusUser > 5) {
                $exception = new MessageBag([
                    'title' => 'Thông báo',
                    'message' => 'Sinh viên không được phép đăng kí vì '. $nameStatus['0'],
                ]);
                return redirect('/user/student')->with(compact('exception'));
            } else {
                if($timeRegister) {
                    return $next($request);
                } else {
                    $exception = new MessageBag([
                        'title' => 'Thông báo',
                        'message' => 'Hiện tại chưa có đợt đăng kí ',
                    ]);
                    return redirect('/user/student')->with(compact('exception'));
                }
            }
        } else {
            $exception = new MessageBag([
                'title' => 'Thông báo',
                'message' => 'Sinh viên không nằm trong khóa được đăng kí',
            ]);
            return redirect('/user/student')->with(compact('exception'));
        }
    }else {
        
             $exception = new MessageBag([
                'title' => 'Thông báo',
                'message' => 'Sinh viên không nằm trong khóa được đăng kí',
            ]);
            return redirect('/user/student')->with(compact('exception'));

    }
    }
}
