<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'action', 'module', 'subject_type', 'subject_id', 'description', 'meta'];

    protected $casts = ['meta' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $action, string $module, string $description, $subjectType = null, $subjectId = null, array $meta = [])
    {
        return static::create([
            'user_id'      => auth()->id(),
            'action'       => $action,
            'module'       => $module,
            'subject_type' => $subjectType,
            'subject_id'   => $subjectId,
            'description'  => $description,
            'meta'         => $meta,
        ]);
    }
}