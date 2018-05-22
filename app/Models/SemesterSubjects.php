<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SemesterSubjects extends Model
{
//    use SoftDeletes;
    protected $table = 'semester_subjects';
//    public function semester() {
//        return $this->belongsTo(Semester::class);
//    }
//    public function subjects() {
//        return $this->belongsTo(Subjects::class);
//    }

}
