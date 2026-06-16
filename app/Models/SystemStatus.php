<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemStatus extends Model
{
    protected $table    = 'system_status';
    protected $fillable = ['status', 'reason', 'resolved_by', 'changed_by', 'changed_at'];
    protected $dates    = ['changed_at'];

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public static function current()
    {
        return static::latest()->first();
    }

    public static function isDown()
    {
        return static::current()?->status === 'down';
    }
}