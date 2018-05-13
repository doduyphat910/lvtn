<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentUser extends Model
{
    use SoftDeletes;

    protected $table = 'student_user';

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
}
