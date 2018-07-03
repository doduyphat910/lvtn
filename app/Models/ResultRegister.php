<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResultRegister extends Model
{
    protected $table = 'result_register';
//    protected $fillable = ['id_subject_register'];
    use SoftDeletes;

//    public function point() {
//        return $this->hasMany(Point::class);
//    }
    public function student_user() {
        return $this->belongsTo(StudentUser::class);
    }
    public function subject_register() {
        return $this->belongsTo(SubjectRegister::class);
    }
    public function time_register() {
        return $this->belongsTo(TimeRegister::class);
    }
}
