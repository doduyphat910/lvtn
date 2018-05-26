<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Year extends Model
{
    use SoftDeletes;
    protected $table = 'year';
    public function semester() {
        return $this->hasMany(Semester::class, 'id_year', 'id');
    }
}
