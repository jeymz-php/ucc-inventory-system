<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationType extends Model
{
    protected $fillable = [
        'type_code', 'type_name', 'campus_id', 'description',
        'icon_class', 'color_primary', 'color_secondary',
        'equipment_label', 'manager_title', 'is_active',
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}