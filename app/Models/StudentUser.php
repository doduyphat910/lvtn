<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Encore\Admin\Auth\Database\Administrator;

class StudentUser  extends Model
{
//    use SoftDeletes;

    protected $table = 'student_user';
    protected $fillable = ['email', 'id_class','code_number' , 'first_name', 'password', 'last_name', 'avatar', 'id_status'];

    public function studentStatus() {
        return $this->belongsTo(Status::class, 'id_status', 'ids');
    }

    public function setPasswordAttribute($password)
    {
        if($password && $password != $this->password) {
            $this->attributes['password'] = bcrypt($password);
        }
    }
    public function comments() {
        return $this->belongsTo(Comments::class, 'id_user', 'id');
    }
}
