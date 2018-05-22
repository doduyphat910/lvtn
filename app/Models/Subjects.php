<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subjects extends Model
{
    use SoftDeletes;
    protected $table = 'subjects';

    public function user_subject() {
        return $this->hasMany(UserSubject::class);
    }
    public function subject_register() {
        return $this->hasMany(SubjectRegister::class);
    }
    public function subject_before_after() {
        return $this->hasMany(SubjectBeforeAfter::class);
    }
    public function subject_group() {
        return $this->belongsTo(SubjectGroup::class);
    }
    public function semester() {
        return $this->belongsToMany(Semester::class);
    }
    public function rate() {
        return $this->belongsTo(Rate::class);
    }
//    public function semester_subjects() {
//        return $this->hasMany(SemesterSubjects::class);
//    }

}
