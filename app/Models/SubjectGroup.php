<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectGroup extends Model
{
    use SoftDeletes;

    protected $table = 'subject_group';
    public function subject() {
        return $this->hasMany(Subjects::class);
    }
}
