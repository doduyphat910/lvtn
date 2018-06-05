<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeRegister extends Model
{
    use SoftDeletes;

    protected $table = 'time_register';

    public function subject_register() {
        return $this->hasMany(SubjectRegister::class);
    }
    public function semester() {
        return $this->belongsTo(Semester::class);
    }
}
