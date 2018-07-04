<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectRegister extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'id';
    protected $keyType  = 'string';
    protected $table = 'subject_register';
    protected $fillable = ['qty_current','id'];
    public $incrementing = false;


    public function user_subject_register() {
        return $this->hasMany(ResultRegister::class);
    }

//    public function different_register() {
//        return $this->hasMany(DifferentRegister::class);
//    }

    public function student_user() {
        return $this->belongsTo(UserAdmin::class);
    }
    public function subject() {
        return $this->belongsTo(Subjects::class);
    }
    public function time_register() {
        return $this->belongsTo(TimeRegister::class);
    }
    public function class_room() {
        return $this->belongsTo(Classroom::class);
    }

    //learning session
    public function time_study(){
        return $this->hasMany(TimeStudy::class, 'id_subject_register','id');
    }

}
