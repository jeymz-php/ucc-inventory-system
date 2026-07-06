<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemUpdate extends Model
{
    protected $fillable = [
        'version', 'title', 'system', 'content', 'show_modal', 'created_by',
    ];

    protected $casts = ['show_modal' => 'boolean'];

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Latest version for a given system ('ims', 'cs', or 'both')
    public static function latest(string $system = 'ims')
    {
        return static::where(function ($q) use ($system) {
            $q->where('system', $system)->orWhere('system', 'both');
        })->latest()->first();
    }

    // Whether to show the modal on login for a given system
    public static function shouldShowModal(string $system = 'ims'): bool
    {
        return (bool) static::where(function ($q) use ($system) {
            $q->where('system', $system)->orWhere('system', 'both');
        })->where('show_modal', true)->exists();
    }

    // Auto-generate next version number
    public static function nextVersion(): string
    {
        $latest = static::latest()->orderBy('id', 'desc')->first();
        if (!$latest) return 'v1.0.0';

        // Parse current version
        $v = ltrim($latest->version, 'v');
        $parts = explode('.', $v);
        $major = (int) ($parts[0] ?? 1);
        $minor = (int) ($parts[1] ?? 0);
        $patch = (int) ($parts[2] ?? 0);

        // Auto-increment patch
        $patch++;
        return "v{$major}.{$minor}.{$patch}";
    }
}