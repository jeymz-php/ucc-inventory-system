<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['ticket_no', 'user_id', 'type', 'status', 'subject'];

    public function user()     { return $this->belongsTo(User::class); }
    public function messages() { return $this->hasMany(Message::class); }
    public function lastMessage() { return $this->hasOne(Message::class)->latestOfMany(); }

    public static function generateTicketNo(): string
    {
        return 'TKT-' . now()->format('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    public function getUnreadCountForAdmin(): int
    {
        return $this->messages()->where('sender_type', 'user')->where('is_read', false)->count();
    }
}