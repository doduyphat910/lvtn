<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeStudy extends Model
{
    protected $table = 'time_study';
    protected $fillable = ['day', 'time_study_start', 'time_study_end'];

    public function subject_registers()
    {
        return $this->belongsTo(SubjectRegister::class, 'id_subject_register');
    }
}
