<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DifferentRegister extends Model
{
    use SoftDeletes;

    protected $table = 'different_register';

    public function student_user() {
        return $this->belongsTo(StudentUser::class);
    }
    public function subject_register() {
        return $this->belongsTo(SubjectRegister::class);
    }
}
