<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
//    use SoftDeletes;
    protected $table = 'status';
    public function studentUser() {
        return $this->hasMany(StudentUsers::class, 'id_status', 'ids');
    }
}
