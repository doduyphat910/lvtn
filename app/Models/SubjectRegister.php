<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectRegister extends Model
{
    use SoftDeletes;

    protected $table = 'subject_register';

    public function user_subject_register() {
        return $this->hasMany(UserSubjectRegister::class);
    }

    public function different_register() {
        return $this->hasMany(DifferentRegister::class);
    }

    public function student_user() {
        return $this->belongsTo(StudentUser::class);
    }
    public function subject() {
        return $this->belongsTo(Subjects::class);
    }
    public function time_register() {
        return $this->belongsTo(TimeRegister::class);
    }
    public function class_room() {
        return $this->belongsTo(Classroom::class);
    }

}
