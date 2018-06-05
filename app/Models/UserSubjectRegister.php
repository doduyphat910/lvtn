<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSubjectRegister extends Model
{
    protected $table = 'user_subject_register';

    public function point() {
        return $this->hasOne(Point::class);
    }
    public function student_user() {
        return $this->belongsTo(UserAdmin::class);
    }
    public function subject_register() {
        return $this->belongsTo(SubjectRegister::class);
    }
}
