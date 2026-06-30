<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeEquipment extends Model
{
    use SoftDeletes;
    
    protected $table = 'office_equipment';
    protected $guarded = [];

    protected $casts = [
        'purchase_date'   => 'date',
        'warranty_expiry' => 'date',
        'condemned_date'  => 'datetime',
        'assigned_date'   => 'datetime',
        'is_condemned'    => 'boolean',
    ];

    public function location() { return $this->belongsTo(Location::class); }
    public function campus()   { return $this->belongsTo(Campus::class); }
    public function assignedUser()  { return $this->belongsTo(User::class, 'assigned_to'); }
    public function condemnedByUser() { return $this->belongsTo(User::class, 'condemned_by'); }

    public function getDisplayNameAttribute() { return $this->article; }
    public function getEquipmentTypeAttribute() { return 'Office'; }
}