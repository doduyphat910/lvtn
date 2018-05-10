<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectBeforeAfter extends Model
{
    use SoftDeletes;

    protected $table = 'subject_before_after';

    public function subject() {
        return $this->belongsTo(Subjects::class);
    }
}
