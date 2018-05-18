<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Encore\Admin\Auth\Database\Administrator;

class TeacherUser  extends Administrator
{
    use SoftDeletes;

//    protected $table = 'teacher_user';
    protected $fillable = ['email', 'id_class','code_number' , 'username', 'password', 'name', 'avatar'];


    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.users_table'));
        parent::__construct($attributes);
    }
}
