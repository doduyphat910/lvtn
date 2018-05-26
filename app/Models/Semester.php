<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\SemesterSubject;
class Semester extends Model
{
    use SoftDeletes;

    protected $table = 'semester';

    public function time_register() {
        return $this->hasMany(TimeRegister::class);
    }
    public function subjects() {
        return $this->belongsToMany(Subjects::class, 'semester_subjects', 'semester_id', 'subjects_id');
    }
    public function year() {
        return $this->belongsTo(Year::class, 'id_year', 'id');
    }

}
