<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    protected $fillable = ['name', 'code', 'is_active'];

    public function departments()
    {
        return $this->hasMany(Department::class);
    }
}