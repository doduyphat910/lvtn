<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Authenticatable;
use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Auth\Database\HasPermissions;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class StudentUser extends Model implements AuthenticatableContract
{
    use Authenticatable, AdminBuilder, HasPermissions;

    protected $fillable = ['email', 'id_class', 'is_teacher', 'username', 'password', 'name', 'avatar'];
    use SoftDeletes;

//    protected $table = 'student_user';

    public function classSTU() {
        return $this->belongsTo(ClassSTU::class);
    }
    public function user_subject() {
        return $this->hasMany(UserSubject::class);
    }
    public function subject_register() {
        return $this->hasMany(SubjectRegister::class);
    }
    public function different_register() {
        return $this->hasMany(DifferentRegister::class);
    }
    public function user_subject_register() {
        return $this->hasMany(UserSubjectRegister::class);
    }
    public function point() {
        return $this->hasMany(Point::class);
    }

    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);
        $this->setTable(config('admin.database.users_table'));
        parent::__construct($attributes);
    }
}
