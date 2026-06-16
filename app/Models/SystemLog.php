<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    protected $fillable = [
        'type', 'title', 'message', 'url',
        'method', 'user_id', 'user_role',
        'ip_address', 'is_resolved',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $type, string $title, string $message = null)
    {
        $user = auth()->user();
        return static::create([
            'type'       => $type,
            'title'      => $title,
            'message'    => $message,
            'url'        => request()->fullUrl(),
            'method'     => request()->method(),
            'user_id'    => $user?->id,
            'user_role'  => $user?->role,
            'ip_address' => request()->ip(),
        ]);
    }
}