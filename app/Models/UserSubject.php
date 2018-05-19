<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSubject extends Model
{
    use SoftDeletes;

    protected $table = 'user_subject';

    public function student_user() {
        return $this->belongsTo(UserAdmin::class);
    }
    public function subject() {
        return $this->belongsTo(Subjects::class);
    }
}
