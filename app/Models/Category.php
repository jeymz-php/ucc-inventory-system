<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'icon_class'];

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }
}