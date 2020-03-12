<?php

namespace App\Http\Extensions\src;

use App\Http\Extensions\GridUser;
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

    public function formUser($model, Closure $callable)
    {
        return new FormUser($this->getModel($model), $callable);
    }

    public function gridUser($model, Closure $callable)
    {
        return new GridUser($this->getModel($model), $callable);
    }

    public static function css($css = null)
    {
        if (!is_null($css)) {
            self::$css = array_merge(self::$css, (array) $css);

            return;
        }

        $css = array_get(Form::collectFieldAssets(), 'css', []);

        static::$css = array_merge(static::$css, $css);

        return view('User.partials.css', ['css' => array_unique(static::$css)]);
    }


}