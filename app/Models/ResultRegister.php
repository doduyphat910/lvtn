<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultRegister extends Model
{
    protected $table = 'result_register';
    protected $fillable = ['id_subject_register'];

    public function point() {
        return $this->hasMany(Point::class);
    }
    public function student_user() {
        return $this->belongsTo(UserAdmin::class);
    }
    public function subject_register() {
        return $this->belongsTo(SubjectRegister::class);
    }
    public function time_register() {
        return $this->belongsTo(TimeRegister::class);
    }
}
