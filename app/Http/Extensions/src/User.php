<?php

namespace App\Http\Extensions\src;

use App\Http\Extensions\LayoutUser\ContentUser;
use Closure;
use Encore\Admin\Admin;
//use Encore\Admin\Auth\Database\Menu;
//use Encore\Admin\Widgets\Navbar;
//use Illuminate\Database\Eloquent\Model as EloquentModel;
//use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Config;
//use Illuminate\Support\Facades\Route;
//use InvalidArgumentException;

/**
 * Class Admin.
 */
class User extends Admin
{
    public function content(Closure $callable = null)
    {
        return new ContentUser($callable);
    }
}