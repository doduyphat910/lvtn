<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Semester extends Model
{
    use SoftDeletes;

    protected $table = 'semester';

    public function time_register() {
        return $this->hasMany(TimeRegister::class);
    }
    public function subject() {
        return $this->hasMany(Subjects::class);
    }
    public function year() {
        return $this->belongsTo(Year::class);
    }

}
