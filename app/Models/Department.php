<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
//    use SoftDeletes;

    protected $table = 'department';
    public function classSTU() {
        return $this->hasMany(ClassSTU::class);
    }
}
