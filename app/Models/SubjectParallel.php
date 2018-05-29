<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectParallel extends Model
{
    use SoftDeletes;

    protected $table = 'subjects_parallel';
}
