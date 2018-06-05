<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeRegister extends Model
{
    use SoftDeletes;

    protected $table = 'time_register';
    protected $fillable = ['school_year'];


//    public function subject_register() {
//        return $this->hasMany(SubjectRegister::class);
//    }
//    public function semester() {
//        return $this->belongsTo(Semester::class);
//    }
    public function setSchoolYearAttribute($schoolYear)
    {
        if(is_array($schoolYear)) {
            $this->attributes['school_year'] = json_encode($schoolYear);
        }
    }

    public function getSchoolYearAttribute($schoolYear)
    {
        return json_decode($schoolYear, true);
    }
}
