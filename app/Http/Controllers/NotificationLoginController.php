<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\Notifications;
use Illuminate\Pagination\Paginator;
class NotificationLoginController extends Controller
{
    public function list()
    {
    	$Notifications = Notifications::paginate(4);
    	return view('User.getLogin',['list'=>$Notifications]);

    }
}
