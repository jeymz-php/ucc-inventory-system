<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemStatus extends Model
{
    protected $table    = 'system_status';
    protected $fillable = ['system', 'status', 'reason', 'resolved_by', 'changed_by', 'changed_at'];
    protected $dates    = ['changed_at'];

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Get current status for a specific system ('ims' or 'cs')
    public static function current(string $system = 'ims')
    {
        return static::where('system', $system)->latest()->first();
    }

    public static function isDown(string $system = 'ims'): bool
    {
        return static::current($system)?->status === 'down';
    }
}