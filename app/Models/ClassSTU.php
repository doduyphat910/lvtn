<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassSTU extends Model
{
    use SoftDeletes;

    protected $table = 'class';

    public function department() {
        return $this->belongsTo(Department::class);
    }
    public function user_teacher() {
        return $this->belongsTo(UserAdmin::class);
    }
}
