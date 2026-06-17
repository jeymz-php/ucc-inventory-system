<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'location_name', 'location_type_id', 'campus_id',
        'description', 'capacity', 'facilitator_id', 'is_active',
    ];

    public function locationType()
    {
        return $this->belongsTo(LocationType::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function facilitator()
    {
        return $this->belongsTo(User::class, 'facilitator_id');
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }
}