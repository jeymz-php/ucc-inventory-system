<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComputerInventory extends Model
{
    protected $table = 'computer_inventory';
    protected $guarded = [];

    protected $casts = [
        'purchase_date'  => 'date',
        'condemned_date' => 'datetime',
        'assigned_date'  => 'datetime',
        'is_condemned'   => 'boolean',
    ];

    public function location() { return $this->belongsTo(Location::class); }
    public function campus()   { return $this->belongsTo(Campus::class); }
    public function assignedUser()  { return $this->belongsTo(User::class, 'assigned_to'); }
    public function condemnedByUser() { return $this->belongsTo(User::class, 'condemned_by'); }

    // Unified display name for "All Equipment" view
    public function getDisplayNameAttribute()
    {
        return $this->computer_set_description;
    }

    public function getEquipmentTypeAttribute()
    {
        return 'Computer';
    }
}