<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ConsumableRequest extends Model
{
    protected $fillable = [
        'reference_no', 'recipient_last_name', 'recipient_first_name', 'recipient_mi',
        'campus_id', 'department', 'request_date', 'approved_by', 'supply_officer',
        'status', 'requested_by', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = ['request_date' => 'date', 'reviewed_at' => 'datetime'];

    public function items() { return $this->hasMany(ConsumableRequestItem::class); }
    public function campus() { return $this->belongsTo(Campus::class); }
    public function requester() { return $this->belongsTo(User::class, 'requested_by'); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function getRecipientNameAttribute()
    {
        return trim("{$this->recipient_first_name} {$this->recipient_mi} {$this->recipient_last_name}");
    }

    public static function generateReferenceNo(): string
    {
        return 'REQ-' . now()->format('Ymd') . '-' . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
    }
}