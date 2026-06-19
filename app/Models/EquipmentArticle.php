<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentArticle extends Model
{
    protected $fillable = ['equipment_type', 'name', 'is_active'];
}