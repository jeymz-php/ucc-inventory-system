<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'equipment';

    protected $fillable = [
        'asset_tag', 'name', 'category_id', 'location_id',
        'status', 'condition', 'brand', 'model', 'serial_number',
        'date_acquired', 'unit_value', 'remarks', 'assigned_to', 'is_condemned',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}