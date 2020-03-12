<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
	protected $table = 'comments';
    public function comments() {
        return $this->hasMany(Comments::class, 'id_user', 'id');
    }
}
